<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\AccountsTransaction;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductMovement;
use App\Models\Lead;
use App\Models\ManufacturerSlaughtering;
use App\Models\Vendor;
use App\Models\VendorTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport()
    {
        // Ambil data laporan penjualan
        $salesData = DB::table('orders')
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select('orders.*', 'vendors.name as vendor_name', 'products.name as product_name')
            ->where('vendors.transaction_type', 'outbound')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $salesData,
            'message' => 'Sales report retrieved successfully.',
        ]);
    }

    public function purchaseReport()
    {
        // Ambil data laporan pembelian
        $purchaseData = DB::table('orders')
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select('orders.*', 'vendors.name as vendor_name', 'products.name as product_name')
            ->where('vendors.transaction_type', 'inbound')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $purchaseData,
            'message' => 'Purchase report retrieved successfully.',
        ]);
    }

    public function revenueReport()
    {
        // Ambil data dari tabel AccountTransactions sesuai dengan laporan pendapatan
        $revenueData = AccountsTransaction::with('account')->where('type', 'Pendapatan')->orWhere('type', 'credit')->get();

        return response()->json([
            'status' => 200,
            'data' => $revenueData,
            'message' => 'Revenue report retrieved successfully.',
        ]);
    }

    public function expensesReport()
    {
        // Ambil data dari tabel AccountTransactions sesuai dengan laporan pengeluaran
        $expensesData = AccountsTransaction::with('account')->where('type', 'Pengeluaran')->orWhere('type', 'debit')->get();

        return response()->json([
            'status' => 200,
            'data' => $expensesData,
            'message' => 'Expenses report retrieved successfully.',
        ]);
    }

    public function inventoryReport()
    {
        // Ambil data dari tabel Product dan ProductMovement sesuai dengan laporan persediaan
        $inventoryData = Product::with('movements')->get();

        return response()->json([
            'status' => 200,
            'data' => $inventoryData,
            'message' => 'Inventory report retrieved successfully.',
        ]);
    }

    public function leadsReport()
    {
        // Ambil data dari tabel Lead sesuai dengan laporan prospek
        $leadsData = Lead::all();

        return response()->json([
            'status' => 200,
            'data' => $leadsData,
            'message' => 'Leads report retrieved successfully.',
        ]);
    }
    public function vendorReport(Request $request)
    {

        // Ambil transaksi vendor terkait
        $transactions = VendorTransaction::with('vendor', 'order')->get();

        return response()->json([
            'status' => 200,
            'data' => [
                'transactions' => $transactions,
            ],
            'message' => 'Vendor report retrieved successfully.',
        ]);
    }
    // Laporan Produksi
    public function productionReport()
    {
        $summary = DB::table('manufacturer_processing_activities')
            ->join('orders', 'manufacturer_processing_activities.order_id', '=', 'orders.id')
            ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
            ->select(
                DB::raw('CONCAT("ORD/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0")) as kodeOrder'),
                'products.name as product_name',
                'manufacturer_processing_activities.activity_type as activity_type',
                'manufacturer_processing_activities.status_activities as status_production'
            )
            ->orderBy('orders.created_at', 'asc')
            ->get();

        if ($summary->isEmpty()) {
            return response()->json(['message' => 'No production activities found.', 'status' => 404], 404);
        }

        return response()->json(['data' => $summary, 'status' => 200, 'message' => 'Production summary retrieved successfully.']);
    }
    public function productionReportDetails($order_id)
    {
        $details = DB::table('manufacturer_processing_activities')
            ->join('orders', 'manufacturer_processing_activities.order_id', '=', 'orders.id')
            ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
            ->where('manufacturer_processing_activities.order_id', '=', $order_id)
            ->select(
                DB::raw('CONCAT("ORD/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0")) as kodeOrder'),
                'products.name as product_name',
                'manufacturer_processing_activities.activity_type',
                'manufacturer_processing_activities.details'
            )
            ->orderBy('manufacturer_processing_activities.created_at', 'asc')
            ->get();

        if ($details->isEmpty()) {
            return response()->json(['message' => 'No details found for this order.', 'status' => 404], 404);
        }

        $formattedDetails = $details->map(function ($item) {
            $item->details = json_decode($item->details, true);
            return $item;
        });

        return response()->json([
            'data' => [
                'kodeOrder' => $details->first()->kodeOrder,
                'product_name' => $details->first()->product_name,
                'activities' => $formattedDetails
            ],
            'status' => 200,
            'message' => 'Production details retrieved successfully.'
        ]);
    }
    // Laporan Neraca
    public function BalanceSheetReport()
    {
        $assets = Account::where('type', 'Aset')->sum('balance');
        $liabilities = Account::where('type', 'Liabilitas')->sum('balance');
        $equity = Account::where('type', 'Ekuitas')->sum('balance');

        return response()->json([
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
        ]);
    }
    // Laporan hutang (piutang usaha)
    public function PayablesReport()
    {
        $vendors = Vendor::with(['orders.invoices' => function ($query) {
            $query->selectRaw('order_id, SUM(total_amount) as total_tagihan, SUM(paid_amount) as total_dibayar')
                ->groupBy('order_id');
        }])->where('transaction_type', 'inbound')->get();

        $payablesReport = $vendors->map(function ($vendor) {
            $vendor->orders->each(function ($order) {
                $order->invoices->each(function ($invoice) use ($order) {
                    $order->total_tagihan = $invoice->total_tagihan ?? 0;
                    $order->total_dibayar = $invoice->total_dibayar ?? 0;
                    $order->sisa_tagihan = $order->total_tagihan - $order->total_dibayar;
                });
            });
            return [
                'vendor_name' => $vendor->name,
                'orders' => $vendor->orders->map(function ($order) {
                    return [
                        'order_id' => $order->id,
                        'total_tagihan' => $order->total_tagihan,
                        'total_dibayar' => $order->total_dibayar,
                        'sisa_tagihan' => $order->sisa_tagihan,
                    ];
                })
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $payablesReport,
        ]);
    }
    // Laporan Piutang
    public function ReceivablesReport()
    {
        $receivables = Invoice::where('status', '!=', 'paid')
            ->whereHas('order.vendor', function ($query) {
                $query->where('transaction_type', 'outbound'); // Asumsi untuk penjualan kepada pelanggan
            })
            ->with(['order.vendor' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get([
                'id', 'order_id', 'total_amount', 'paid_amount', 'balance_due', 'status'
            ])
            ->map(function ($invoice) {
                $invoice->total_tagihan = $invoice->total_amount;
                $invoice->total_dibayar = $invoice->paid_amount;
                $invoice->sisa_tagihan = $invoice->balance_due;
                $invoice->vendor_name = $invoice->order->vendor->name; // Di sini vendor_name mewakili nama pelanggan
                $invoice->kode_order = $invoice->order->kode_order;

                unset($invoice->order);

                return $invoice;
            });

        return response()->json([
            'status' => 200,
            'data' => $receivables,
        ]);
    }
    // Laporan arus kas
    public function CashFlowReport()
    {
        $cashRelatedAccounts = Account::whereIn('type', ['Aset', 'Liabilitas'])
            ->get(['id', 'name', 'type', 'balance']);

        // Untuk setiap akun, hitung total debit dan kredit dari transactions
        $cashFlowDetails = $cashRelatedAccounts->map(function ($account) {
            $totalDebit = $account->transactions()->where('type', 'debit')->sum('amount');
            $totalCredit = $account->transactions()->where('type', 'credit')->sum('amount');

            // Hitung perubahan saldo untuk periode laporan, jika perlu
            $netCashFlow = $totalDebit - $totalCredit;

            return [
                'account_name' => $account->name,
                'type' => $account->type,
                'opening_balance' => $account->balance, // Anggap balance sebagai saldo awal
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'net_cash_flow' => $netCashFlow,
                // 'closing_balance' => $account->balance + $netCashFlow, // Jika perlu hitung saldo akhir
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $cashFlowDetails,
        ]);
    }
    public function LedgerReport()
    {
        $accounts = Account::with(['journalEntries' => function ($query) {
            $query->orderBy('date', 'asc');
        }])->get();

        $ledgerReport = $accounts->map(function ($account) {
            // Inisialisasi saldo berjalan
            $runningBalance = 0;

            $entries = $account->journalEntries->map(function ($entry) use (&$runningBalance) {
                // Hitung saldo berjalan
                $runningBalance += ($entry->debit - $entry->credit);

                return [
                    'date' => $entry->date,
                    'description' => $entry->description,
                    'debit' => $entry->debit,
                    'credit' => $entry->credit,
                    'running_balance' => $runningBalance,
                ];
            });

            return [
                'account_name' => $account->name,
                'account_type' => $account->type,
                'entries' => $entries,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $ledgerReport,
        ]);
    }
    public function generateCashLedgerReport(Request $request)
    {
        // Menerima account_id dari request
        $cashAccountId = $request->input('account_id');

        // Validasi untuk memastikan account_id disediakan
        if (!$cashAccountId) {
            return response()->json([
                'status' => 400,
                'message' => 'Account ID is required.',
            ]);
        }

        $cashEntries = JournalEntry::where('account_id', $cashAccountId)
            ->orderBy('date', 'asc')
            ->get();

        $runningBalance = 0; // Inisialisasi saldo kas awal

        $cashLedgerReport = $cashEntries->map(function ($entry) use (&$runningBalance) {
            // Hitung saldo berjalan untuk kas
            $runningBalance += ($entry->debit - $entry->credit);

            return [
                'date' => $entry->date,
                'description' => $entry->description,
                'debit' => $entry->debit,
                'credit' => $entry->credit,
                'running_balance' => $runningBalance,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $cashLedgerReport,
        ]);
    }
}
