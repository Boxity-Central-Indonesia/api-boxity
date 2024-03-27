<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hitung total penjualan
    $totalSales = Order::where('order_status', 'Completed')->whereHas('vendor', function ($query) {
        $query->where('transaction_type', 'outbound');
    })->sum('total_price');

    // Hitung total pembelian
    $totalPurchases = Order::where('order_status', 'Completed')->whereHas('vendor', function ($query) {
        $query->where('transaction_type', 'inbound');
    })->sum('total_price');

    // Hitung total pembayaran
    $totalPayments = Invoice::sum('paid_amount');

    // Hitung profit
    $totalProfit = $totalSales - $totalPurchases;

    // Hitung data statistik order penjualan dan pembelian per bulan
    // Ambil data statistik penjualan per tanggal
    $salesData = Order::whereHas('vendor', function ($query) {
        $query->where('transaction_type', 'outbound');
    })
    ->selectRaw('DATE(created_at) as date, SUM(total_price) as total_sales')
    ->groupBy('date')
    ->orderBy('date')
    ->get();

    // Ambil data statistik pembelian per tanggal
    $purchaseData = Order::whereHas('vendor', function ($query) {
        $query->where('transaction_type', 'inbound');
    })
    ->selectRaw('DATE(created_at) as date, SUM(total_price) as total_purchases')
    ->groupBy('date')
    ->orderBy('date')
    ->get();

    // Membuat data dalam format yang diharapkan oleh ReactJS
    $salesByDate = [];
    $purchasesByDate = [];

    foreach ($salesData as $data) {
    $salesByDate[] = [
        'date' => $data->date,
        'total_sales' => (int)$data->total_sales,
    ];
    }

    foreach ($purchaseData as $data) {
    $purchasesByDate[] = [
        'date' => $data->date,
        'total_purchases' => (int)$data->total_purchases,
    ];
    }
    // Hitung total penjualan minggu ini
    $startOfMonth = now()->startOfMonth();
    $endOfMonth = now()->endOfMonth();

    $totalSalesThisMonth = Order::whereHas('vendor', function ($query) {
            $query->where('transaction_type', 'outbound');
        })->whereHas('invoices', function ($query) {
            $query->where('status', 'partial')->OrWhere('status','paid');
        })
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->sum('total_price');


    return response()->json([
        'status' => 200,
        'data' => [
            [
                'label' => 'Total Sales',
                'amount' => 'Rp ' . number_format($totalSales, 0, ',', '.'),
                'information' => 'Information about total sales',
            ],
            [
                'label' => 'Total Purchases',
                'amount' => 'Rp ' . number_format($totalPurchases, 0, ',', '.'),
                'information' => 'Information about total purchases',
            ],
            [
                'label' => 'Total Paid',
                'amount' => 'Rp ' . number_format($totalPayments, 0, ',', '.'),
                'information' => 'Information about total payments',
            ],
            [
                'label' => 'Profit',
                'amount' => 'Rp ' . number_format($totalProfit, 0, ',', '.'),
                'information' => 'Information about profit',
            ],
            [
                'sales_data' => $salesByDate,
                'purchase_data' => $purchasesByDate,
            ],
            [
                'label'=>'Total sales this month',
                'total_sales_this_month' => 'Rp ' . number_format($totalSalesThisMonth, 0, ',', '.'),
            ]
        ],
        'message' => 'Data retrieved successfully.',
    ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
