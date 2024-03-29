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
    $cachedData = Cache::remember('index_data', now()->addMinutes(10), function () {
        // Hitung total penjualan, pembelian, dan pembayaran dalam satu query
        $sales = Order::where('order_status', 'Completed')->whereHas('vendor', function ($query) {
            $query->where('transaction_type', 'outbound');
        })->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_price');

        $purchases = Order::where('order_status', 'Completed')->whereHas('vendor', function ($query) {
            $query->where('transaction_type', 'inbound');
        })->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_price');

        $payments = Invoice::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('paid_amount');

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
        })->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_price');

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
    $product->percentage = ($product->total_stock * 100) / $total_all_stock;
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
    });

    return response()->json([
        'status' => 200,
        'data' => [
            [
                'label' => 'Total Sales',
                'amount' => 'Rp ' . number_format($cachedData['sales'], 0, ',', '.'),
                'information' => $cachedData['sales'] >= 0 ? 'This is the total sales amount, keep it up!' : 'Looks like your total sales amount is negative, please review your sales strategy!',
            ],
            [
                'label' => 'Total Purchases',
                'amount' => 'Rp ' . number_format($cachedData['purchases'], 0, ',', '.'),
                'information' => $cachedData['purchases'] >= 0 ? 'Here\'s the total amount spent on purchases, keep an eye on it!' : 'Your total purchases amount is in the negative, consider optimizing your purchasing process!',
            ],
            [
                'label' => 'Total Paid',
                'amount' => 'Rp ' . number_format($cachedData['payments'], 0, ',', '.'),
                'information' => $cachedData['payments'] >= 0 ? 'So far, you\'ve paid a total of, keep managing your finances wisely!' : 'It seems your total paid amount is negative, ensure proper management of your payments!',
            ],
            [
                'label' => 'Profit',
                'amount' => 'Rp ' . number_format($cachedData['profit'], 0, ',', '.'),
                'information' => $cachedData['profit'] >= 0 ? 'Great news! This is your profit, keep the momentum going!' : 'Your profit amount is showing a negative value, evaluate your business operations to enhance profitability!',
            ],
            [
                'sales_data' => $cachedData['sales_data'],
                'purchase_data' => $cachedData['purchase_data'],
            ],
            [
                'label' => 'Total sales this month',
                'total_sales_this_month' => 'Rp ' . number_format($cachedData['total_sales_this_month'], 0, ',', '.'),
            ],
            [
                'label' => 'Products with Most Sales',
                'products' => $cachedData['products'] ? $cachedData['products']->toArray() : 0,
            ],
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
