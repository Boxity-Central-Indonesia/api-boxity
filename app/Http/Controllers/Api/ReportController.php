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
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport()
    {
        $salesData = DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
            ->join('invoices', 'orders.id', '=', 'invoices.order_id')
            ->select(
                'orders.*',
                'vendors.name as vendor_name',
                'products.name as product_name',
                'order_products.quantity',
                'order_products.price_per_unit',
                'invoices.paid_amount',
                'invoices.invoice_date',
                DB::raw('invoices.status as invoice_status'),
                DB::raw('order_products.quantity * order_products.price_per_unit as total_price'),
                DB::raw('CASE
                        WHEN vendors.transaction_type = "inbound" THEN CONCAT("PO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        WHEN vendors.transaction_type = "outbound" THEN CONCAT("SO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        ELSE CONCAT("ORD/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                    END as kode_order')
            )
            ->where('vendors.transaction_type', 'outbound')
            ->get()->map(function ($salesData) {
                $salesData->total_price = (int) $salesData->total_price;
                $salesData->paid_amount = (int) $salesData->paid_amount;
                $salesData->taxes = (int) $salesData->taxes;
                $salesData->shipping_cost = (int) $salesData->shipping_cost;
                $salesData->price_per_unit = (int) $salesData->price_per_unit;
                return $salesData;
            });
            $salesData = $salesData->groupBy('kode_order')->map(function ($orders) {
                $mergedOrder = $orders->first();
                $mergedOrder->total_price = $orders->sum('total_price');
                $mergedOrder->quantity = $orders->sum('quantity');
                return $mergedOrder;
            })->values()->all();

        return response()->json([
            'status' => 200,
            'data' => $salesData,
            'message' => 'Sales report retrieved successfully.',
        ]);
    }
    public function downloadSalesReportPdf()
    {
        $salesData = DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
            ->join('invoices', 'orders.id', '=', 'invoices.order_id')
            ->select(
                'orders.*',
                'vendors.name as vendor_name',
                'products.name as product_name',
                'order_products.quantity',
                'order_products.price_per_unit',
                'invoices.paid_amount',
                'invoices.invoice_date',
                DB::raw('invoices.status as invoice_status'),
                DB::raw('order_products.quantity * order_products.price_per_unit as total_price'),
                DB::raw('CASE
                        WHEN vendors.transaction_type = "inbound" THEN CONCAT("PO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        WHEN vendors.transaction_type = "outbound" THEN CONCAT("SO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        ELSE CONCAT("ORD/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                    END as kode_order')
            )
            ->where('vendors.transaction_type', 'outbound')
            ->get()->map(function ($salesData) {
                $salesData->total_price = (int) $salesData->total_price;
                $salesData->paid_amount = (int) $salesData->paid_amount;
                $salesData->taxes = (int) $salesData->taxes;
                $salesData->shipping_cost = (int) $salesData->shipping_cost;
                $salesData->price_per_unit = (int) $salesData->price_per_unit;
                return $salesData;
            });
            $salesData = $salesData->groupBy('kode_order')->map(function ($orders) {
                $mergedOrder = $orders->first();
                $mergedOrder->total_price = $orders->sum('total_price');
                $mergedOrder->quantity = $orders->sum('quantity');
                return $mergedOrder;
            })->values()->all();

    // Validasi jika data persediaan tidak ditemukan
    if (empty($salesData)) {
        return response()->json(['message' => 'No sales report data found.', 'status' => 404], 404);
    }
    $pdf = PDF::loadView('pdf.sales_report', compact('salesData'));

    // Generate nama file dengan menambahkan tanggal
    $fileName = 'sales_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

    // Simpan file PDF di storage dengan nama yang baru
    $pdf->save(public_path('pdf/' . $fileName));

    // Mendapatkan URL untuk di-download
    $pdfUrl = url('pdf/' . $fileName);

    // Mengirim response dengan URL file yang dapat di-download
    return response()->json([
        'message' => 'PDF generated successfully.',
        'data' => $pdfUrl,
        'status' => 200,
    ]);
    }

    public function purchaseReport()
    {
        $purchaseData = DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
            ->join('invoices', 'orders.id', '=', 'invoices.order_id')
            ->select(
                'orders.*',
                'vendors.name as vendor_name',
                'products.name as product_name',
                'order_products.quantity',
                'order_products.price_per_unit',
                'invoices.paid_amount',
                'invoices.invoice_date',
                DB::raw('invoices.status as invoice_status'),
                DB::raw('order_products.quantity * order_products.price_per_unit as total_price'),
                DB::raw('CASE
                        WHEN vendors.transaction_type = "inbound" THEN CONCAT("PO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        WHEN vendors.transaction_type = "outbound" THEN CONCAT("SO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        ELSE CONCAT("ORD/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                    END as kode_order')
            )
            ->where('vendors.transaction_type', 'inbound')
            ->get()->map(function ($purchaseData) {
                $purchaseData->total_price = (int) $purchaseData->total_price;
                $purchaseData->paid_amount = (int) $purchaseData->paid_amount;
                $purchaseData->taxes = (int) $purchaseData->taxes;
                $purchaseData->shipping_cost = (int) $purchaseData->shipping_cost;
                $purchaseData->price_per_unit = (int) $purchaseData->price_per_unit;
                return $purchaseData;
            });
            $purchaseData = $purchaseData->groupBy('kode_order')->map(function ($orders) {
                $mergedOrder = $orders->first();
                $mergedOrder->total_price = $orders->sum('total_price');
                $mergedOrder->quantity = $orders->sum('quantity');
                return $mergedOrder;
            })->values()->all();

        return response()->json([
            'status' => 200,
            'data' => $purchaseData,
            'message' => 'Purchase report retrieved successfully.',
        ]);
    }
    public function downloadPurchaseReportPdf()
    {
        // Panggil fungsi inventoryReport untuk mendapatkan data persediaan
        $purchaseData = DB::table('orders')
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->join('products', 'order_products.product_id', '=', 'products.id')
        ->join('vendors', 'orders.vendor_id', '=', 'vendors.id')
        ->join('invoices', 'orders.id', '=', 'invoices.order_id')
        ->select(
            'orders.*',
            'vendors.name as vendor_name',
            'products.name as product_name',
            'order_products.quantity',
            'order_products.price_per_unit',
            'invoices.paid_amount',
            'invoices.invoice_date',
            DB::raw('invoices.status as invoice_status'),
            DB::raw('order_products.quantity * order_products.price_per_unit as total_price'),
            DB::raw('CASE
                        WHEN vendors.transaction_type = "inbound" THEN CONCAT("PO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        WHEN vendors.transaction_type = "outbound" THEN CONCAT("SO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        ELSE CONCAT("ORD/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                    END as kode_order')
        )
        ->where('vendors.transaction_type', 'inbound')
        ->get()->map(function ($purchaseData) {
            $purchaseData->total_price = (int) $purchaseData->total_price;
            $purchaseData->paid_amount = (int) $purchaseData->paid_amount;
            $purchaseData->taxes = (int) $purchaseData->taxes;
            $purchaseData->shipping_cost = (int) $purchaseData->shipping_cost;
            $purchaseData->price_per_unit = (int) $purchaseData->price_per_unit;
            return $purchaseData;
        });
        $purchaseData = $purchaseData->groupBy('kode_order')->map(function ($orders) {
            $mergedOrder = $orders->first();
            $mergedOrder->total_price = $orders->sum('total_price');
            $mergedOrder->quantity = $orders->sum('quantity');
            return $mergedOrder;
        })->values()->all();

    // Validasi jika data persediaan tidak ditemukan
    if (empty($purchaseData)) {
        return response()->json(['message' => 'No purchase report data found.', 'status' => 404], 404);
    }
    $pdf = PDF::loadView('pdf.purchase_report', compact('purchaseData'));

    // Generate nama file dengan menambahkan tanggal
    $fileName = 'purchase_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

    // Simpan file PDF di storage dengan nama yang baru
    $pdf->save(public_path('pdf/' . $fileName));

    // Mendapatkan URL untuk di-download
    $pdfUrl = url('pdf/' . $fileName);

    // Mengirim response dengan URL file yang dapat di-download
    return response()->json([
        'message' => 'PDF generated successfully.',
        'data' => $pdfUrl,
        'status' => 200,
    ]);
}

    public function revenueReport()
    {
        // Ambil data dari tabel AccountTransactions sesuai dengan laporan pendapatan
        $revenueData = AccountsTransaction::with('account')->where('type', 'Pendapatan')->orWhere('type', 'credit')->get()->map(function ($revenueData) {
            $revenueData->amount = (int) $revenueData->amount;
            $revenueData->account->balance = (int) $revenueData->account->balance;
            return $revenueData;
        });

        return response()->json([
            'status' => 200,
            'data' => $revenueData,
            'message' => 'Revenue report retrieved successfully.',
        ]);
    }
    public function downloadRevenueReportPdf(){
        $revenueData = AccountsTransaction::with('account')->where('type', 'Pendapatan')->orWhere('type', 'credit')->get()->map(function ($revenueData) {
            $revenueData->amount = (int) $revenueData->amount;
            $revenueData->account->balance = (int) $revenueData->account->balance;
            return $revenueData;
        });

        if ($revenueData->isEmpty()) {
            return response()->json(['message' => 'No revenue report data found.', 'status' => 404], 404);
        }
        $pdf = PDF::loadView('pdf.revenue_report', compact('revenueData'));

        // Generate nama file dengan menambahkan tanggal
        $fileName = 'revenue_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Simpan file PDF di storage dengan nama yang baru
        $pdf->save(public_path('pdf/' . $fileName));

        // Mendapatkan URL untuk di-download
        $pdfUrl = url('pdf/' . $fileName);

        // Mengirim response dengan URL file yang dapat di-download
        return response()->json([
            'message' => 'PDF generated successfully.',
            'data' => $pdfUrl,
            'status' => 200,
        ]);
    }

    public function expensesReport()
    {
        // Ambil data dari tabel AccountTransactions sesuai dengan laporan pengeluaran
        $expensesData = AccountsTransaction::with('account')->where('type', 'Pengeluaran')->orWhere('type', 'debit')->get()->map(function ($expensesData) {
            $expensesData->amount = (int) $expensesData->amount;
            $expensesData->account->balance = (int) $expensesData->account->balance;
            return $expensesData;
        });

        return response()->json([
            'status' => 200,
            'data' => $expensesData,
            'message' => 'Expenses report retrieved successfully.',
        ]);
    }
    public function downloadExpensesReportPdf(){
        $expensesData = AccountsTransaction::with('account')->where('type', 'Pengeluaran')->orWhere('type', 'debit')->get()->map(function ($expensesData) {
            $expensesData->amount = (int) $expensesData->amount;
            $expensesData->account->balance = (int) $expensesData->account->balance;
            return $expensesData;
        });

        if ($expensesData->isEmpty()) {
            return response()->json(['message' => 'No expenses report data found.', 'status' => 404], 404);
        }
        $pdf = PDF::loadView('pdf.expenses_report', compact('expensesData'));

        // Generate nama file dengan menambahkan tanggal
        $fileName = 'expenses_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Simpan file PDF di storage dengan nama yang baru
        $pdf->save(public_path('pdf/' . $fileName));

        // Mendapatkan URL untuk di-download
        $pdfUrl = url('pdf/' . $fileName);

        // Mengirim response dengan URL file yang dapat di-download
        return response()->json([
            'message' => 'PDF generated successfully.',
            'data' => $pdfUrl,
            'status' => 200,
        ]);
    }

    public function inventoryReport()
    {
        // Ambil data dari tabel Product dan ProductMovement sesuai dengan laporan persediaan
        $inventoryData = Product::with('movements')
            ->where('stock', '>', 0)
            ->get()
            ->map(function ($item) {
                $item->price = (int) $item->price;
                $item->total_price = (int) ($item->price * $item->stock);
                return $item;
            });

        return response()->json([
            'status' => 200,
            'data' => $inventoryData,
            'message' => 'Inventory report retrieved successfully.',
        ]);
    }
    public function downloadInventoryReportPdf()
    {
        // Panggil fungsi inventoryReport untuk mendapatkan data persediaan
        $inventoryData = Product::with('movements','category','warehouse')
        ->where('stock', '>', 0)
        ->get()
        ->map(function ($item) {
            $item->price = (int) $item->price;
            $item->total_price = (int) ($item->price * $item->stock);
            return $item;
        });

    // Validasi jika data persediaan tidak ditemukan
    if ($inventoryData->isEmpty()) {
        return response()->json(['message' => 'No inventory data found.', 'status' => 404], 404);
    }

    $pdf = PDF::loadView('pdf.inventory_report', compact('inventoryData'))->setPaper('a4', 'landscape');

    // Generate nama file dengan menambahkan tanggal
    $fileName = 'inventory_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

    // Simpan file PDF di storage dengan nama yang baru
    $pdf->save(public_path('pdf/' . $fileName));

    // Mendapatkan URL untuk di-download
    $pdfUrl = url('pdf/' . $fileName);

    // Mengirim response dengan URL file yang dapat di-download
    return response()->json([
        'message' => 'PDF generated successfully.',
        'data' => $pdfUrl,
        'status' => 200,
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
        // Ambil transaksi vendor terkait dengan detail order dan product
        $transactions = VendorTransaction::with(['vendor', 'order' => function ($query) {
            $query->select('id', 'created_at');
        }, 'product' => function ($query) {
            $query->select('id', 'name');
        }])->get()->map(function ($transactions) {
            $transactions->amount = (int) $transactions->amount;
            $transactions->unit_price = (int) $transactions->unit_price;
            $transactions->taxes = (int) $transactions->taxes;
            $transactions->shipping_cost = (int) $transactions->shipping_cost;
            $transactions->total_price = (int) $transactions->total_price;
            return $transactions;
        });

        // Tampilkan hanya data yang diperlukan dan tentukan apakah pesanan merupakan PO atau SO
        $filteredTransactions = $transactions->map(function ($transaction) {
            $kodeOrder = $transaction->order
                ? ($transaction->vendor->transaction_type === 'inbound' ? 'PO/' : 'SO/') . $transaction->order->created_at->format('Y/m/') . str_pad($transaction->order->id, 4, '0', STR_PAD_LEFT)
                : 'N/A'; // Atau format default jika order tidak ada
            return [
                'kode_order' => $kodeOrder,
                'nama_vendor' => $transaction->vendor->name,
                'nama_product' => $transaction->product ? $transaction->product->name : 'N/A', // Cek apakah product tersedia
                'amount' => $transaction->amount,
                'unit_price' => $transaction->unit_price,
                'taxes' => $transaction->taxes,
                'shipping_cost' => $transaction->shipping_cost,
                'total_price' => $transaction->total_price,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $filteredTransactions,
            'message' => 'Vendor report retrieved successfully.',
        ]);
    }

    public function downloadVendorReportPdf()
    {
        // Ambil transaksi vendor terkait dengan detail order dan product
        $transactions = VendorTransaction::with(['vendor', 'order' => function ($query) {
            $query->select('id', 'created_at');
        }, 'product' => function ($query) {
            $query->select('id', 'name');
        }])->get()->map(function ($transactions) {
            $transactions->amount = (int) $transactions->amount;
            $transactions->unit_price = (int) $transactions->unit_price;
            $transactions->taxes = (int) $transactions->taxes;
            $transactions->shipping_cost = (int) $transactions->shipping_cost;
            $transactions->total_price = (int) $transactions->total_price;
            return $transactions;
        });

        // Tampilkan hanya data yang diperlukan dan tentukan apakah pesanan merupakan PO atau SO
        $filteredTransactions = $transactions->map(function ($transaction) {
            $kodeOrder = $transaction->order
                ? ($transaction->vendor->transaction_type === 'inbound' ? 'PO/' : 'SO/') . $transaction->order->created_at->format('Y/m/') . str_pad($transaction->order->id, 4, '0', STR_PAD_LEFT)
                : 'N/A'; // Atau format default jika order tidak ada
            return [
                'kode_order' => $kodeOrder,
                'nama_vendor' => $transaction->vendor->name,
                'nama_product' => $transaction->product ? $transaction->product->name : 'N/A', // Cek apakah product tersedia
                'amount' => $transaction->amount,
                'unit_price' => $transaction->unit_price,
                'taxes' => $transaction->taxes,
                'shipping_cost' => $transaction->shipping_cost,
                'total_price' => $transaction->total_price,
            ];
        });

        // Validasi jika data tidak ditemukan
        if ($filteredTransactions->isEmpty()) {
            return response()->json(['message' => 'No vendor data found.', 'status' => 404], 404);
        }

        $pdf = PDF::loadView('pdf.vendor_report', compact('filteredTransactions'))->setPaper('a4', 'landscape');

        // Generate nama file dengan menambahkan tanggal
        $fileName = 'vendor_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Simpan file PDF di storage dengan nama yang baru
        $pdf->save(public_path('pdf/' . $fileName));

        // Mendapatkan URL untuk di-download
        $pdfUrl = url('pdf/' . $fileName);

        // Mengirim response dengan URL file yang dapat di-download
        return response()->json([
            'message' => 'PDF generated successfully.',
            'data' => $pdfUrl,
            'status' => 200,
        ]);
    }


    // Laporan Produksi
    public function productionReport()
    {
        $activities = DB::table('manufacturer_processing_activities')
            ->join('orders', 'manufacturer_processing_activities.order_id', '=', 'orders.id')
            ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
            ->select(
                DB::raw('CASE
                            WHEN orders.order_type = "inbound" THEN CONCAT("PO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                            WHEN orders.order_type = "outbound" THEN CONCAT("SO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                            ELSE CONCAT("ORD/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        END as kodeOrder'),
                'products.name as product_name',
                'manufacturer_processing_activities.activity_type as activity_type',
                'manufacturer_processing_activities.status_activities as status_production',
                'manufacturer_processing_activities.created_at as tanggal_aktifitas'
            )
            ->orderBy('orders.created_at', 'asc')
            ->get();

        if ($activities->isEmpty()) {
            return response()->json(['message' => 'No production activities found.', 'status' => 404], 404);
        }

        // Mengelompokkan data berdasarkan kodeOrder
        $groupedActivities = $activities->groupBy('kodeOrder')->map(function ($items, $kodeOrder) {
            // Ambil detail produk dari item pertama karena diasumsikan semua item dalam grup memiliki produk yang sama
            $firstItem = $items->first();

            return [
                'kodeOrder' => $kodeOrder,
                'product_name' => $firstItem->product_name,
                'activities' => $items->map(function ($item) {
                    return [
                        'activity_type' => $item->activity_type,
                        'status_production' => $item->status_production,
                        'tanggal_aktifitas' => $item->tanggal_aktifitas,
                    ];
                })->values()->all(), // Pastikan untuk mereset index array
            ];
        })->values()->all(); // Konversi hasil dari map ke array numerik untuk respons JSON

        if (empty($groupedActivities)) {
            return response()->json(['message' => 'No production activities found.', 'status' => 404], 404);
        }

        return response()->json(['data' => $groupedActivities, 'status' => 200, 'message' => 'Production summary retrieved successfully.']);
    }

    public function downloadProductionReportPdf()
    {
        $activities = DB::table('manufacturer_processing_activities')
            ->join('orders', 'manufacturer_processing_activities.order_id', '=', 'orders.id')
            ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
            ->select(
                DB::raw('CASE
                            WHEN orders.order_type = "inbound" THEN CONCAT("PO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                            WHEN orders.order_type = "outbound" THEN CONCAT("SO/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                            ELSE CONCAT("ORD/", YEAR(orders.created_at), "/", MONTH(orders.created_at), "/", LPAD(orders.id, 4, "0"))
                        END as kodeOrder'),
                'products.name as product_name',
                'manufacturer_processing_activities.activity_type as activity_type',
                'manufacturer_processing_activities.status_activities as status_production',
                'manufacturer_processing_activities.created_at as tanggal_aktifitas'
            )
            ->orderBy('orders.created_at', 'asc')
            ->get();

        // Mengelompokkan data berdasarkan kodeOrder
        $groupedActivities = $activities->groupBy('kodeOrder')->map(function ($items, $kodeOrder) {
            // Ambil detail produk dari item pertama karena diasumsikan semua item dalam grup memiliki produk yang sama
            $firstItem = $items->first();

            return [
                'kodeOrder' => $kodeOrder,
                'product_name' => $firstItem->product_name,
                'activities' => $items->map(function ($item) {
                    return [
                        'activity_type' => $item->activity_type,
                        'status_production' => $item->status_production,
                        'tanggal_aktifitas' => $item->tanggal_aktifitas,
                    ];
                })->values()->all(), // Pastikan untuk mereset index array
            ];
        })->values()->all(); // Konversi hasil dari map ke array numerik untuk respons JSON

        // Validasi jika data tidak ditemukan
        if (empty($groupedActivities)) {
            return response()->json(['message' => 'No production data found.', 'status' => 404], 404);
        }

        $pdf = PDF::loadView('pdf.production_report', compact('groupedActivities'))->setPaper('a4', 'landscape');

        // Generate nama file dengan menambahkan tanggal
        $fileName = 'production_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Simpan file PDF di storage dengan nama yang baru
        $pdf->save(public_path('pdf/' . $fileName));

        // Mendapatkan URL untuk di-download
        $pdfUrl = url('pdf/' . $fileName);

        // Mengirim response dengan URL file yang dapat di-download
        return response()->json([
            'message' => 'PDF generated successfully.',
            'data' => $pdfUrl,
            'status' => 200,
        ]);
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

        $assets = (int) $assets;
    $liabilities = (int) $liabilities;
    $equity = (int) $equity;

        return response()->json([
            'data' => [
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $equity,
            ],
            'status' => 200,
            'message' => 'Balance sheets retrieved successfully.'
        ]);
    }
    public function downloadBalanceSheetReportPdf(){
        // Sum the balances for each account type
        $assets = Account::where('type', 'Aset')->sum('balance');
        $liabilities = Account::where('type', 'Liabilitas')->sum('balance');
        $equity = Account::where('type', 'Ekuitas')->sum('balance');

        // Convert to integers if necessary
        $assets = (int) $assets;
        $liabilities = (int) $liabilities;
        $equity = (int) $equity;

        // Load the PDF view with the correct variables
        $pdf = PDF::loadView('pdf.balance_sheet_report', compact('assets', 'liabilities', 'equity'))
            ->setPaper('a4', 'landscape');

        // Generate the file name with the current date and time
        $fileName = 'balance_sheet_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Save the PDF file in the public/pdf directory
        $pdf->save(public_path('pdf/' . $fileName));

        // Get the URL for downloading the PDF
        $pdfUrl = url('pdf/' . $fileName);

        // Return a JSON response with the download link
        return response()->json([
            'message' => 'PDF generated successfully.',
            'data' => $pdfUrl,
            'status' => 200,
        ]);
    }


    // Laporan hutang (piutang usaha)
    public function PayablesReport()
    {
        $vendors = Vendor::with(['orders' => function ($query) {
            // Pastikan untuk memilih 'created_at' juga
            $query->select('id', 'vendor_id', 'created_at');
        }, 'orders.invoices' => function ($query) {
            $query->selectRaw('order_id, SUM(total_amount) as total_tagihan, SUM(paid_amount) as total_dibayar')
                ->groupBy('order_id');
        }])->where('transaction_type', 'inbound')->get();

        $payablesReport = $vendors->map(function ($vendor) {
            $vendor->orders->each(function ($order) {
                // Tambahkan kode_order ke setiap order
                $order->kode_order = 'ORD/' . $order->created_at->format('Y/m/') . str_pad($order->id, 4, '0', STR_PAD_LEFT);
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
                        'kode_order' => $order->kode_order,
                        'total_tagihan' => (int)$order->total_tagihan,
                        'total_dibayar' => (int)$order->total_dibayar,
                        'sisa_tagihan' => (int)$order->sisa_tagihan,
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
        $vendors = Vendor::with(['orders' => function ($query) {
            // Pastikan untuk memilih 'created_at' juga
            $query->select('id', 'vendor_id', 'created_at');
        }, 'orders.invoices' => function ($query) {
            $query->selectRaw('order_id, SUM(total_amount) as total_tagihan, SUM(paid_amount) as total_dibayar')
                ->groupBy('order_id');
        }])->where('transaction_type', 'outbound')->get();

        $receivables = $vendors->map(function ($vendor) {
            $vendor->orders->each(function ($order) {
                // Tambahkan kode_order ke setiap order
                $order->kode_order = 'ORD/' . $order->created_at->format('Y/m/') . str_pad($order->id, 4, '0', STR_PAD_LEFT);
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
                        'kode_order' => $order->kode_order,
                        'total_tagihan' => (int)$order->total_tagihan,
                        'total_dibayar' => (int)$order->total_dibayar,
                        'sisa_tagihan' => (int)$order->sisa_tagihan,
                    ];
                })
            ];
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
                'opening_balance' => (int)$account->balance, // Anggap balance sebagai saldo awal
                'total_debit' => (int)$totalDebit,
                'total_credit' => (int)$totalCredit,
                'net_cash_flow' => (int)$netCashFlow,
                // 'closing_balance' => $account->balance + $netCashFlow, // Jika perlu hitung saldo akhir
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $cashFlowDetails,
        ]);
    }
    public function downloadCashFlowReportPdf()
    {
        // Get Cash Flow details from the CashFlowReport function
        $response = $this->CashFlowReport();

        // If there's an error, return the error response
        if ($response->getStatusCode() != 200) {
            return $response;
        }

        $data = $response->original['data'];

        // Load the PDF view with the correct variables
        $pdf = PDF::loadView('pdf.cash_flow_report', compact('data'))
            ->setPaper('a4', 'landscape');

        // Generate the file name with the current date and time
        $fileName = 'cash_flow_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

        // Save the PDF file in the public/pdf directory
        $pdf->save(public_path('pdf/' . $fileName));

        // Get the URL for downloading the PDF
        $pdfUrl = url('pdf/' . $fileName);

        // Return a JSON response with the download link
        return response()->json([
            'message' => 'PDF generated successfully.',
            'data' => $pdfUrl,
            'status' => 200,
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
                'debit' => (int)$entry->debit,
                'credit' => (int)$entry->credit,
                'running_balance' => (int)$runningBalance,
            ];
        });

        // Tambahkan entri default jika tidak ada entri jurnal
        if ($entries->isEmpty()) {
            $entries->push([
                'date' => '0000-00-00',
                'description' => 'No Description',
                'debit' => 0,
                'credit' => 0,
                'running_balance' => 0,
            ]);
        }

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


    public function downloadLedgerReportPdf()
{
    // Get Ledger Report details from the LedgerReport function
    $response = $this->LedgerReport();

    // If there's an error, return the error response
    if ($response->getStatusCode() != 200) {
        return $response;
    }

    $data = $response->original['data'];

    // Load the PDF view with the correct variables
    $pdf = PDF::loadView('pdf.ledger_report', compact('data'))
        ->setPaper('a4', 'landscape');

    // Generate the file name with the current date and time
    $fileName = 'ledger_report_' . Carbon::now()->format('Ymd_His') . '.pdf';

    // Save the PDF file in the public/pdf directory
    $pdf->save(public_path('pdf/' . $fileName));

    // Get the URL for downloading the PDF
    $pdfUrl = url('pdf/' . $fileName);

    // Return a JSON response with the download link
    return response()->json([
        'message' => 'PDF generated successfully.',
        'data' => $pdfUrl,
        'status' => 200,
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
                'debit' => (int)$entry->debit,
                'credit' => (int)$entry->credit,
                'running_balance' => (int)$runningBalance,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $cashLedgerReport,
        ]);
    }
}
