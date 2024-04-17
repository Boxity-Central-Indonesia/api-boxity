<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order;
use Carbon\Carbon;
use Cache;
use App\Models\Product;
use DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menggunakan cache jika perlu
        $cachedData = Cache::remember('index_data', now()->addMinutes(1), function () {
            try {
                // Hitung total penjualan, pembelian, dan pembayaran dalam satu query
                $sales = Order::where('order_status', 'Completed')->whereHas('vendor', function ($query) {
                    $query->where('transaction_type', 'outbound');
                })->sum('total_price');

                $purchases = Order::where('order_status', 'Completed')->whereHas('vendor', function ($query) {
                    $query->where('transaction_type', 'inbound');
                })->sum('total_price');

                $payments = Invoice::sum('paid_amount');

                $profit = $sales - $purchases;

                // Hitung data statistik penjualan dan pembelian per bulan
                $salesData = Order::whereHas('vendor', function ($query) {
                    $query->where('transaction_type', 'outbound');
                })->selectRaw('DATE(created_at) as date, SUM(total_price) as total_sales')->groupBy('date')->orderBy('date')->get();

                $purchaseData = Order::whereHas('vendor', function ($query) {
                    $query->where('transaction_type', 'inbound');
                })->selectRaw('DATE(created_at) as date, SUM(total_price) as total_purchases')->groupBy('date')->orderBy('date')->get();

                $totalSalesThisMonth = Order::whereHas('vendor', function ($query) {
                    $query->where('transaction_type', 'outbound');
                })->whereHas('invoices', function ($query) {
                    $query->whereIn('status', ['partial', 'paid']);
                })->sum('total_price');

                // Query to find products ordered by the number of sales/orders
                $total_all_stock = DB::table('order_products')->sum('quantity');

                $products = Product::select('name', 'code', 'unit_of_measure')
                    ->withCount(['orders as order_counted' => function ($query) {
                        $query->selectRaw('sum(quantity) as order_counted');
                        $query->whereHas('vendor', function ($query) {
                            $query->where('transaction_type', 'outbound');
                        });
                    }])
                    ->selectRaw('(SELECT CAST(IFNULL(sum(quantity), 0) AS UNSIGNED) FROM order_products WHERE order_products.product_id = products.id) as total_stock')
                    ->orderByDesc('total_stock')
                    ->limit(5)
                    ->get();

                foreach ($products as $product) {
                    $product->total_all_stock = (int)$total_all_stock;
                    $product->percentage = $total_all_stock != 0 ? ($product->total_stock * 100) / $total_all_stock : 0;
                }

                return [
                    'sales' => $sales,
                    'purchases' => $purchases,
                    'payments' => $payments,
                    'profit' => $profit,
                    'sales_data' => $salesData,
                    'purchase_data' => $purchaseData,
                    'total_sales_this_month' => $totalSalesThisMonth,
                    'products' => $products,
                ];
            } catch (\Exception $e) {
                // Tangani error dengan baik
                \Log::error('Error fetching dashboard data: ' . $e->getMessage());
                return [];
            }
        });

        return response()->json([
            'status' => 200,
            'data' => [
                [
                    'label' => 'Total Sales',
                    'amount' => 'Rp ' . number_format($cachedData['sales'], 0, ',', '.'),
                    'information' => $cachedData['sales'] >= 0 ? 'Total penjualan Anda, pertahankan!' : 'Total penjualan Anda negatif, periksa strategi penjualan Anda!',
                ],
                [
                    'label' => 'Total Purchases',
                    'amount' => 'Rp ' . number_format($cachedData['purchases'], 0, ',', '.'),
                    'information' => $cachedData['purchases'] >= 0 ? 'Total belanja Anda, perhatikan!' : 'Total belanja Anda negatif, pertimbangkan untuk mengoptimalkan proses pembelian Anda!',
                ],
                [
                    'label' => 'Total Paid',
                    'amount' => 'Rp ' . number_format($cachedData['payments'], 0, ',', '.'),
                    'information' => $cachedData['payments'] >= 0 ? 'Total pembayaran yang telah Anda bayar, kelola keuangan Anda dengan bijak!' : 'Total pembayaran yang telah Anda bayar negatif, pastikan pengelolaan pembayaran Anda dengan baik!',
                ],
                [
                    'label' => 'Profit',
                    'amount' => 'Rp ' . number_format($cachedData['profit'], 0, ',', '.'),
                    'information' => $cachedData['profit'] >= 0 ? 'Berita baik! Ini adalah keuntungan Anda, pertahankan momentumnya!' : 'Jumlah keuntungan Anda menunjukkan nilai negatif, evaluasi operasi bisnis Anda untuk meningkatkan profitabilitas!',
                ],
                [
                    'sales_data' => $cachedData['sales_data'],
                    'purchase_data' => $cachedData['purchase_data'],
                ],
                [
                    'label' => 'Total penjualan bulan ini',
                    'total_sales_this_month' => 'Rp ' . number_format($cachedData['total_sales_this_month'], 0, ',', '.'),
                ],
                [
                    'label' => 'Produk dengan Penjualan Terbanyak',
                    'products' => $cachedData['products'] ? $cachedData['products']->toArray() : 0,
                ],
            ],
            'message' => 'Data berhasil diambil.',
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