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
        $reportData = ManufacturerSlaughtering::with(['carcasses.processingActivities', 'carcasses.viscera', 'carcasses.packaging'])
            ->get();

        $formattedReport = [];

        foreach ($reportData as $slaughter) {
            foreach ($slaughter->carcass as $carcass) {
                $activities = [
                    [
                        'activity_type' => 'Slaughtering',
                        'details' => [
                            'method' => $slaughter->method,
                            'slaughter_date' => $slaughter->slaughter_date,
                        ],
                    ],
                ];

                // Check if 'viscera' relationship exists, if not, use an empty array
                $visceraActivities = $carcass->viscera ? $carcass->viscera : [];
                foreach ($visceraActivities as $viscera) {
                    $activities[] = [
                        'activity_type' => 'Viscera Handling',
                        'details' => [
                            'type' => $viscera->type,
                            'handling_method' => $viscera->handling_method,
                        ],
                    ];
                }

                // Check if 'processingActivities' relationship exists, if not, use an empty array
                $processingActivities = $carcass->processingActivities ? $carcass->processingActivities : [];
                foreach ($processingActivities as $activity) {
                    $activities[] = [
                        'activity_type' => $activity->activity_type,
                        'details' => $activity->details,
                    ];
                }

                // Check if 'packaging' relationship exists, if not, use an empty array
                $packagingActivity = $carcass->packaging ? $carcass->packaging : [];
                $activities[] = [
                    'activity_type' => 'Packaging',
                    'details' => [
                        'weight' => $packagingActivity->weight ?? null,
                        'package_type' => $packagingActivity->package_type ?? null,
                    ],
                ];

                $formattedReport[] = [
                    'slaughtering_date' => $slaughter->slaughter_date,
                    'carcass_weight' => $carcass->weight_after_slaughter,
                    'carcass_quality_grade' => $carcass->quality_grade,
                    'activities' => $activities,
                ];
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $formattedReport,
            'message' => 'Production report retrieved successfully.',
        ]);
    }
}
