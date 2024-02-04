<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\AccountBalance;
use App\Models\AccountsTransaction;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductMovement;
use App\Models\Lead;
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
}