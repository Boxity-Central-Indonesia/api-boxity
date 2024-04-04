<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentRequest;
use App\Models\Account;
use App\Models\AccountsBalance;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice; // Pastikan Anda memiliki model ini
use App\Models\ProductsPrice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // Untuk transaksi database
use App\Events\formCreated;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('invoice')->get()->map(function ($payments) {
            $payments->amount_paid = (int) $payments->amount_paid;
            return $payments;
        });
        return response()->json([
            'status' => 200,
            'data' => $payments,
            'message' => 'Payments retrieved successfully.',
        ]);
    }

    public function downloadPaymentDetail($paymentId)
{
    $payment = Payment::with('invoice.order.vendor')->find($paymentId);
    if (!$payment) {
        return response()->json(['message' => 'Payment not found.', 'status' => 404], 404);
    }

    // Format data pembayaran
    $formattedOrder = [
        'id' => $payment->id,
        'kode_payment' => $payment->kode_payment,
        'amount_paid' => (int)$payment->amount_paid,
        'invoice' => [
            'total_amount' => (int)$payment->invoice->total_amount,
            'paid_amount' => (int)$payment->invoice->paid_amount,
            'balance_due' => (int)$payment->invoice->balance_due,
            'invoice_date' => $payment->invoice->invoice_date,
            'due_date' => $payment->invoice->due_date,
            'status' => $payment->invoice->status,
            'kode_invoice' => $payment->invoice->kode_invoice,
            'created_at' => $payment->invoice->created_at,
        ],
        'vendor' => [
            'name' => $payment->invoice->order->vendor->name,
            'address' => $payment->invoice->order->vendor->address,
            'phone_number' => $payment->invoice->order->vendor->phone_number,
            'transaction_type' => $payment->invoice->order->vendor->transaction_type,
        ],
    ];

    // Generate nama file dengan menambahkan tanggal
    $fileName = 'payment_receipt_' . $formattedOrder['id'] . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

    // Mendapatkan URL untuk di-download
    $pdfUrl = url('pdf/' . $fileName);
    $now = Carbon::now();
    $filenameQR = 'qrcode_' . $now->format('Ymd_His') . '.png';
    $qrCodePath = public_path('qrcodes/' . $filenameQR);

    // Generate QR Code
    $qrCode = QrCode::size(100)->generate($pdfUrl);

    // Save QR Code as an image
    Storage::disk('public')->put('qrcodes/' . $filenameQR, $qrCode);
    $pdf = PDF::loadView('pdf.payment_detail', compact('formattedOrder', 'pdfUrl', 'qrCodePath'));

    // Simpan file PDF di storage dengan nama yang baru
    $pdf->save(public_path('pdf/' . $fileName));

    return response()->json([
        'message' => 'PDF generated successfully.',
        'data' => $pdfUrl,
        'status' => 200,
    ]);
}


    public function store(PaymentRequest $request)
    {

        DB::beginTransaction();

        try {
            $validated = $request->validated();
            $payment = Payment::create($validated);
            $invoice = Invoice::with('order.vendor')->find($validated['invoice_id']);

            // Pastikan invoice dan order terkait ditemukan
            if (!$invoice || !$invoice->order) {
                throw new \Exception("Invoice or related order not found.");
            }

            $order = $invoice->order;
            $productPrice = ProductsPrice::where('product_id', $order->product_id)->first();

            if ($productPrice) {
                if ($order->vendor->transaction_type === 'outbound') {
                    // Untuk penjualan, menyesuaikan selling_price berdasarkan markup dari harga beli
                    $markupPercentage = 20; // Misal, markup 20%
                    $adjustedSellingPrice = $productPrice->buying_price * (1 + ($markupPercentage / 100));

                    // Jika ada diskon yang diterapkan, misal 10% dari harga jual yang telah disesuaikan
                    $discountPercentage = 10; // Misal, diskon 10%
                    $adjustedDiscountPrice = $adjustedSellingPrice * (1 - ($discountPercentage / 100));

                    // Perbarui harga jual dan diskon produk
                    $productPrice->selling_price = $adjustedSellingPrice;
                    $productPrice->discount_price = $adjustedDiscountPrice;
                } else {
                    // Untuk pembelian, perbarui buying_price berdasarkan pembayaran
                    $newBuyingPrice = $order->total_price / $order->quantity;
                    $productPrice->buying_price = $newBuyingPrice;
                }
                $productPrice->save();
            }

            // Perbarui balance_due pada invoice setelah pembayaran berhasil dibuat
            $invoice->paid_amount += $validated['amount_paid'];
            $invoice->balance_due = $invoice->total_amount - $invoice->paid_amount;

            // Tentukan status invoice berdasarkan balance_due
            if ($invoice->balance_due <= 0) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }

            $invoice->save();

            // Update saldo Kas dan Piutang Usaha sesuai dengan transaksi pembayaran
            // Anda perlu menyesuaikan account_id sesuai dengan tabel accounts
            $kasAccount = Account::where('name', 'Kas')->first();
            $piutangAccount = Account::where('name', 'Piutang Usaha')->first();

            if ($kasAccount && $piutangAccount) {
                // Meningkatkan saldo Kas
                AccountsBalance::updateOrCreate(
                    ['account_id' => $kasAccount->id, 'date' => now()->toDateString()],
                    ['balance' => DB::raw("balance + {$validated['amount_paid']}")]
                );

                // Mengurangi saldo Piutang Usaha
                AccountsBalance::updateOrCreate(
                    ['account_id' => $piutangAccount->id, 'date' => now()->toDateString()],
                    ['balance' => DB::raw("balance - {$validated['amount_paid']}")]
                );
            }

            DB::commit();

            // Update status order menjadi 'completed'
            $order->status = 'completed';
            $order->save();

            return response()->json([
                'status' => 201,
                'data' => $payment,
                'message' => 'Payment added successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to add payment. Error: ' . $e->getMessage(),
            ]);
        }
    }
    protected function determinePaymentAccountId(Invoice $invoice): ?int
    {
        // Example logic, adjust according to your application
        // Assuming "Kas" is used for all incoming payments
        $account = Account::where('name', 'Kas')->first();
        return $account ? $account->id : null;
    }

    protected function updateAccountBalance($accountId, $amount, $type)
    {
        $accountBalance = AccountsBalance::firstOrCreate(
            ['account_id' => $accountId, 'date' => now()->toDateString()],
            ['balance' => 0]
        );

        // Assuming 'credit' increases the balance and 'debit' decreases it
        if ($type === 'credit') {
            $accountBalance->balance += $amount;
        } else {
            $accountBalance->balance -= $amount;
        }

        $accountBalance->save();
    }

    public function show($id)
    {
        $payment = Payment::with('invoice.order.vendor')->find($id);

        if (!$payment) {
            return response()->json([
                'status' => 404,
                'message' => 'Payment not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $payment,
            'message' => 'Payment retrieved successfully.',
        ]);
    }

    public function update(PaymentRequest $request, $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'status' => 404,
                'message' => 'Payment not found.',
            ]);
        }

        DB::beginTransaction();
        $validated = $request->validated();
        try {
            $amountDifference = $validated['amount_paid'] - $payment->amount_paid;

            // Adjust invoice and account balance
            $invoice = Invoice::with('order.vendor')->find($payment->invoice_id);

            if (!$invoice || !$invoice->order) {
                throw new \Exception("Invoice or related order not found.");
            }

            $order = $invoice->order;

            $invoice->paid_amount += $amountDifference;
            $invoice->balance_due = $invoice->total_amount - $invoice->paid_amount;

            // Tentukan status invoice berdasarkan balance_due
            if ($invoice->balance_due <= 0) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }

            $invoice->save();

            // Assuming Kas account for received payments
            $account_id = $this->determinePaymentAccountId($invoice);
            $this->updateAccountBalance($account_id, $amountDifference, $amountDifference > 0 ? 'credit' : 'debit');

            $payment->update($validated);

            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => $payment,
                'message' => 'Payment updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update payment. Error: ' . $e->getMessage(),
            ]);
        }
    }



    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'status' => 404,
                'message' => 'Payment not found.',
            ]);
        }

        DB::beginTransaction();

        try {
            $invoice = Invoice::find($payment->invoice_id);
            $invoice->paid_amount -= $payment->amount_paid;
            $invoice->balance_due = $invoice->total_amount - $invoice->paid_amount;
            $invoice->status = $invoice->balance_due > 0 ? 'partial' : 'paid';
            $invoice->save();

            // Assuming Kas account for received payments
            $account_id = $this->determinePaymentAccountId($invoice);
            $this->updateAccountBalance($account_id, $payment->amount_paid, 'debit');

            $payment->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Payment deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete payment. Error: ' . $e->getMessage(),
            ]);
        }
    }
}
