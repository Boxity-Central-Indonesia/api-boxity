<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\AccountsTransaction;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order; // Pastikan Anda memiliki model ini
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
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'total_amount' => 'required|numeric',
            'balance_due' => 'required|numeric',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,partial,paid',
        ]);

        DB::beginTransaction();

        try {
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

            $description = "Invoice #{$invoice->id} created based on order #{$order->id}";
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

            DB::commit();

            return response()->json([
                'status' => 200,
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

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return response()->json([
                'status' => 404,
                'message' => 'Invoice not found.',
            ]);
        }

        $validated = $request->validate([
            'total_amount' => 'sometimes|required|numeric',
            'balance_due' => 'sometimes|required|numeric',
            'invoice_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:unpaid,partial,paid',
        ]);

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
                'status' => 200,
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
