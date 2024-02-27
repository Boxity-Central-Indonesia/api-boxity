<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PaymentRequest;
use App\Models\Account;
use App\Models\AccountsBalance;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Invoice; // Pastikan Anda memiliki model ini
use App\Models\ProductsPrice;
use Illuminate\Support\Facades\DB; // Untuk transaksi database

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
                'status' => 200,
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
        $payment = Payment::with('invoice')->find($id);

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
                'status' => 200,
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
