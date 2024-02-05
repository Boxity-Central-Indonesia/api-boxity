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
        $summary = DB::table('manufacturer_manufacturer_processing_activities')
            ->join('orders', 'manufacturer_manufacturer_processing_activities.order_id', '=', 'orders.id')
            ->join('products', 'manufacturer_manufacturer_processing_activities.product_id', '=', 'products.id')
            ->select(
                DB::raw('CONCAT("ORD/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0")) as kodeOrder'),
                'products.name as product_name',
                'manufacturer_manufacturer_processing_activities.activity_type as activity_type',
                'manufacturer_manufacturer_processing_activities.status_activities as status_production'
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
}
