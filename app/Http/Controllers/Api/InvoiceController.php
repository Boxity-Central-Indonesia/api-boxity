<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\InvoiceRequest;
use App\Models\Account;
use App\Models\AccountsTransaction;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order; // Pastikan Anda memiliki model ini
use App\Models\ProductsPrice;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected function determineAccountId(Order $order, string $transactionType): ?int
    {
        // Contoh: Menentukan account_id berdasarkan tipe vendor dan tipe transaksi
        // Misalkan Anda memiliki akun "Accounts Receivable" dan "Accounts Payable" yang sudah ditentukan
        if ($order->vendor->transaction_type === 'outbound') {
            // Jika vendor outbound (menjual), gunakan "Piutang Usaha" untuk Accounts Receivable
            $account = Account::where('name', 'Piutang Usaha')->first();
        } else {
            // Jika vendor inbound (membeli), gunakan "Utang Usaha" untuk Accounts Payable
            $account = Account::where('name', 'Utang Usaha')->first();
        }

        return $account ? $account->id : null;
    }
    public function index()
    {
        $invoices = Invoice::with('order', 'payments')->get();
        return response()->json([
            'status' => 200,
            'data' => $invoices,
            'message' => 'Invoices retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->all();
            $invoice = Invoice::create($validated);

            // Mengambil order dan vendor terkait
            $order = Order::with('vendor')->find($validated['order_id']);
            if (!$order) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Order not found.',
                ]);
            }

            $transactionType = $order->vendor->transaction_type === 'outbound' ? 'credit' : 'debit';

            // Menentukan account_id berdasarkan logika bisnis Anda
            $account_id = $this->determineAccountId($order, $transactionType); // Implementasikan fungsi ini

            $description = "Invoice #{$invoice->id} created based on order #{$order->kode_order}";
            AccountsTransaction::create([
                'account_id' => $account_id,
                'date' => now(),
                'type' => $transactionType,
                'amount' => $invoice->total_amount,
                'description' => $description,
            ]);

            // Peningkatan Piutang Usaha
            if ($transactionType === 'credit') {
                // Temukan atau buat record Piutang Usaha untuk vendor
                $accountsReceivable = Account::firstOrCreate([
                    'name' => 'Piutang Usaha',
                    'type' => 'Aset',
                ]);

                // Tambahkan saldo Piutang Usaha
                $accountsReceivable->balance += $invoice->total_amount;
                $accountsReceivable->save();
            }

            $this->updateProductPricesBasedOnInvoice($order, $invoice, $order->vendor->transaction_type);
            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => $invoice,
                'message' => 'Invoice created successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create invoice. Error: ' . $e->getMessage(),
            ]);
        }
    }
    private function updateProductPricesBasedOnInvoice($order, $invoice, $transactionType)
    {
        $productPrice = ProductsPrice::where('product_id', $order->product_id)->first();

        if (!$productPrice) {
            return; // Early return jika tidak ada data harga produk
        }

        if ($transactionType === 'outbound') {
            // Untuk penjualan, perbarui selling_price
            $newSellingPrice = $this->calculateNewSellingPriceBasedOnInvoice($invoice->total_amount, $order->quantity);
            $productPrice->update(['selling_price' => $newSellingPrice]);
        } elseif ($transactionType === 'inbound') {
            // Untuk pembelian, perbarui buying_price
            // Ini mungkin tidak umum dalam konteks invoice, tetapi untuk keperluan ilustrasi:
            $newBuyingPrice = $this->calculateNewBuyingPriceBasedOnInvoice($invoice->total_amount, $order->quantity);
            $productPrice->update(['buying_price' => $newBuyingPrice]);
        }
    }

    private function calculateNewSellingPriceBasedOnInvoice($totalAmount, $quantity)
    {
        // Logika perhitungan harga jual berdasarkan total jumlah invoice dan kuantitas
        return $totalAmount / $quantity;
    }

    private function calculateNewBuyingPriceBasedOnInvoice($totalAmount, $quantity)
    {
        // Logika perhitungan harga beli mungkin serupa atau berbeda, tergantung kebijakan
        return $totalAmount / $quantity;
    }

    public function show($id)
    {
        $invoice = Invoice::with('order', 'payments')->find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 404,
                'message' => 'Invoice not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $invoice,
            'message' => 'Invoice retrieved successfully.',
        ]);
    }

    public function update(InvoiceRequest $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json([
                'status' => 404,
                'message' => 'Invoice not found.',
            ]);
        }

        DB::beginTransaction();

        try {
            $order = Order::with('vendor')->find($invoice->order_id);
            if (!$order) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Related order not found.',
                ]);
            }

            $transactionType = $order->vendor->transaction_type === 'outbound' ? 'credit' : 'debit';

            // Tentukan account_id berdasarkan logika bisnis Anda
            $account_id = $this->determineAccountId($order, $transactionType); // Implementasikan fungsi ini

            // Cari transaksi yang terkait dengan invoice ini
            $accountTransaction = AccountsTransaction::where('description', 'like', "%Invoice #{$invoice->id} created%")->first();

            if ($accountTransaction) {
                // Perbarui transaksi jika total_amount invoice berubah
                if (isset($validated['total_amount']) && $validated['total_amount'] != $invoice->total_amount) {
                    $description = "Invoice #{$invoice->id} updated";
                    $accountTransaction->update([
                        'account_id' => $account_id,
                        'date' => now(),
                        'type' => $transactionType,
                        'amount' => $validated['total_amount'],
                        'description' => $description,
                    ]);
                }
            } else {
                // Jika tidak ditemukan transaksi, buat baru (opsional, tergantung kebutuhan)
                $description = "Invoice #{$invoice->id} updated";
                AccountsTransaction::create([
                    'account_id' => $account_id,
                    'date' => now(),
                    'type' => $transactionType,
                    'amount' => $invoice->total_amount,
                    'description' => $description,
                ]);
            }

            // Perbarui invoice
            $validated = $request->validated();
            $invoice->update($validated);

            // Pembaruan status invoice dan saldo Piutang Usaha
            if ($transactionType === 'credit') {
                // Jika invoice memiliki balance_due yang nol, maka status menjadi 'paid'
                if ($invoice->balance_due <= 0) {
                    $invoice->status = 'paid';
                    $accountsReceivable = Account::where('name', 'Piutang Usaha')->first();
                    if ($accountsReceivable) {
                        // Kurangi saldo Piutang Usaha
                        $accountsReceivable->balance -= $invoice->total_amount;
                        $accountsReceivable->save();
                    }
                } else {
                    $invoice->status = 'partial';
                }
                $invoice->save();
            }

            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => $invoice,
                'message' => 'Invoice updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update invoice. Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'status' => 404,
                'message' => 'Invoice not found.',
            ]);
        }

        $invoice->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Invoice deleted successfully.',
        ]);
    }
}
