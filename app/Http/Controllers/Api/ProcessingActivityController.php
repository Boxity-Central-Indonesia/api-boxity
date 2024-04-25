<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProcessingActivityRequest;
use App\Models\Order;
use App\Models\ProcessingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\Events\formCreated;
use App\Models\Product;

class ProcessingActivityController extends Controller
{
    public function index()
    {
        // Ubah 'carcass' menjadi 'product' untuk memuat relasi produk
        // $activities = ProcessingActivity::all();
        $activities = ProcessingActivity::with('order', 'product')
                    ->orderBy('order_id')
                    ->orderBy('product_id')
                    ->get()
                    ->groupBy(['order_id', 'product_id']);

        return response()->json([
            'status' => 200,
            'data' => $activities,
            'message' => 'All processing activities retrieved successfully.',
        ]);
    }
    public function getProcessActivityToday()
    {
        try {
            $activities = DB::table('manufacturer_processing_activities')
                ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
                ->leftJoin('orders', 'manufacturer_processing_activities.order_id', '=', 'orders.id')
                ->leftJoin('vendors', 'orders.vendor_id', '=', 'vendors.id')
                ->selectRaw('
                    manufacturer_processing_activities.activity_date,
                    CASE
                        WHEN vendors.transaction_type = "inbound" THEN CONCAT("PO/", DATE_FORMAT(orders.created_at, "%Y/%m/"), LPAD(orders.id, 4, "0"))
                        WHEN vendors.transaction_type = "outbound" THEN CONCAT("SO/", DATE_FORMAT(orders.created_at, "%Y/%m/"), LPAD(orders.id, 4, "0"))
                        ELSE CONCAT("ORD/unknown_date/", LPAD(orders.id, 4, "0"))
                    END AS kode_order,
                    orders.*,
                    manufacturer_processing_activities.created_at,
                    products.name,
                    manufacturer_processing_activities.status_activities,
                    manufacturer_processing_activities.activity_type,
                    manufacturer_processing_activities.details,
                    products.code
                ')
                ->whereDate('manufacturer_processing_activities.created_at', today())
                ->orderByDesc('manufacturer_processing_activities.created_at')
                ->get();

            if ($activities->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No processing activities found today.',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $activities,
                'message' => 'All processing activities today retrieved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve processing activities. Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getProcessActivityWeighingExorder()
    {
        try {
            $activities = DB::table('manufacturer_processing_activities')
                ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
                ->selectRaw('
                    manufacturer_processing_activities.activity_date,
                    products.name,
                    manufacturer_processing_activities.status_activities,
                    manufacturer_processing_activities.activity_type,
                    manufacturer_processing_activities.details,
                    products.code
                ')->orderByDesc('manufacturer_processing_activities.created_at')
                ->get();

            if ($activities->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No processing activities found today.',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $activities,
                'message' => 'All processing activities today retrieved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to retrieve processing activities. Error: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function store(ProcessingActivityRequest $request)
{
    $validated = $request->validated();

    // Ambil data order terlebih dahulu
    $order = Order::findOrFail($validated['order_id']);

    $activities = [];

    foreach ($order->products as $product) {
        $activity = ProcessingActivity::create([
            'product_id' => $product->id,
            'order_id' => $order->id,
            'activity_type' => 'weight_based_ordering',
            'activity_date' => now(),
            'status_activities' => 'In Production',
            'details' => [
                'ordered_quantity' => $order->id,
                'description' => 'Start to production...'
            ],
        ]);

        $activities[] = $activity; // Menyimpan aktivitas pemrosesan ke dalam array
    }

    // Cek dan tandai order sebagai completed jika memenuhi kriteria
    $this->markOrderAsCompleted($order->id);
    broadcast(new formCreated('New Processing activity created successfully.'));

    return response()->json([
        'status' => 201,
        'data' => $activities,
        'message' => 'Processing activities created successfully.', // Perbarui pesan untuk menunjukkan aktivitas jamak
    ]);
}

public function storeTimbangan(Request $request)
    {

        // Ambil data order terlebih dahulu
        $order = Order::findOrFail($request['order_id']);

        $average_weight_per_animal = 0;

        if ($request->details['number_of_item'] != 0) {
            $average_weight_per_animal = ($request->details['qty_weighing'] - $request->details['basket_weight']) / $request->details['number_of_item'];
        }

        $activity = ProcessingActivity::create([
            'product_id' => $request->product_id,
            'order_id' => $order->id,
            'activity_type' => 'weighing',
            'activity_date' => now(),
            'status_activities' => 'In Production',
            'details' => [
                'qty_weighing' => $request->details['qty_weighing'],
                'noa_weighing' => 'Kg',
                'basket_weight' => $request->details['basket_weight'],
                'noa_basket_weight' => 'Kg',
                'number_of_item' => $request->details['number_of_item'],
                'noa_numberofitem' => 'Pcs',
                'average_weight_per_animal' => $average_weight_per_animal,
                'vehicle_no'=>$request->details['vehicle_no'],
                'description' => 'Weighing incoming product based on order'
            ],
        ]);
        $product = Product::where('id', $activity->product_id)->first();
        $product->weight = $activity->details['average_weight_per_animal'];
        $product->stock += $activity->details['number_of_item'];
        $product->save();
        broadcast(new formCreated('New weighing incoming product created successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $activity,
            'message' => 'Weighing incoming product created successfully.', // Perbarui pesan untuk menunjukkan aktivitas jamak
        ]);
        // return response()->json($request->details['number_of_item']);
    }

public function storeTimbanganNotOrdered(Request $request)
    {
        $average_weight_per_animal = 0;

        if ($request->details['number_of_item'] != 0) {
            $average_weight_per_animal = ($request->details['qty_weighing'] - $request->details['basket_weight']) / $request->details['number_of_item'];
        }

        // Buat aktivitas timbangan
    $activity = ProcessingActivity::create([
        'product_id' => $request->product_id,
        'activity_type' => 'weighing',
        'activity_date' => now(),
        'status_activities' => 'In Production',
        'details' => [
            'qty_weighing' => $request->details['qty_weighing'],
            'noa_weighing' => 'Kg',
            'number_of_item' => $request->details['number_of_item'],
            'noa_numberofitem' => 'Pcs',
            'average_weight_per_animal' => $average_weight_per_animal,
            'type_of_item'=> $request->details['type_of_item'],
            'description' => 'Weighing incoming carcass/parting/sampingan product'
        ],
    ]);

    // Update stok produk dengan rata-rata berat per hewan dan jumlah hewan
    $product = Product::find($request->product_id);
    if ($product) {
        $product->weight = $activity->details['average_weight_per_animal'];
        $product->stock += $activity->details['number_of_item'];
        $product->save();
    } else {
        // Handle jika produk tidak ditemukan
        return response()->json([
            'status' => 404,
            'message' => 'Product not found.',
        ], 404);
    }

    // Kirim respons JSON
    return response()->json([
        'status' => 201,
        'data' => $activity,
        'message' => 'Weighing incoming carcass/parting/sampingan product and updated inventory successfully.',
    ]);
}

public function update(ProcessingActivityRequest $request, $id)
{
    $activity = ProcessingActivity::findOrFail($id);

    $validated = $request->validated();

    // Buat entri baru dengan menggunakan metode create
    $newActivity = ProcessingActivity::create([
        'product_id' => $activity->product_id, // Tetapkan product_id yang sama
        'order_id' => $activity->order_id, // Tetapkan order_id yang sama
        'activity_type' => $validated['activity_type'] ?? $activity->activity_type, // Tetapkan nilai baru jika disediakan, jika tidak, gunakan nilai yang sudah ada
        'activity_date' => now(), // Gunakan waktu sekarang untuk entri baru
        'status_activities' => $validated['status_activities'] ?? $activity->status_activities, // Tetapkan nilai baru jika disediakan, jika tidak, gunakan nilai yang sudah ada
        'details' => $validated['details'] ?? $activity->details, // Tetapkan nilai baru jika disediakan, jika tidak, gunakan nilai yang sudah ada
    ]);

    broadcast(new formCreated('Processing activity updated successfully.'));

    return response()->json([
        'status' => 201,
        'data' => $newActivity, // Kirim entri baru sebagai respons
        'message' => 'New processing activity created successfully.', // Pesan yang sesuai dengan tindakan
    ]);
}


    private function markOrderAsCompleted($order_id)
    {
        $statusActivity = ProcessingActivity::where('order_id', $order_id)->first(); // Ubah ke pencarian berdasarkan order_id

        if ($statusActivity && $statusActivity->status_activities !== 'Completed') { // Tambahkan pemeriksaan null di sini
            $lastActivityType = 'packaging_weighing'; // Misalkan aktivitas terakhir yang menandakan order selesai

            $lastActivityExists = ProcessingActivity::where('order_id', $order_id)
                ->where('activity_type', $lastActivityType)
                ->exists();

            if ($lastActivityExists) {
                $statusActivity->status_activities = 'Completed';
                $statusActivity->save();
            }
        }
    }


    public function show($id)
    {
        // Ubah 'carcass' menjadi 'product' untuk memuat relasi produk
        $activity = ProcessingActivity::with('product', 'order')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $activity,
            'message' => 'Processing activity retrieved successfully.',
        ]);
    }


    public function destroy($id)
    {
        ProcessingActivity::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Processing activity deleted successfully.',
        ]);
    }
    public function getActivitiesByOrder($order_id)
{
    $activities = DB::table('manufacturer_processing_activities')
        ->join('orders', 'manufacturer_processing_activities.order_id', '=', 'orders.id')
        ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
        ->join('vendors', 'orders.vendor_id', '=', 'vendors.id') // Join ke tabel vendors
        ->where('manufacturer_processing_activities.order_id', $order_id)
        ->select(
            'manufacturer_processing_activities.*',
            'orders.order_status as order_status',
            'products.name as product_name',
            'products.description as product_description',
            DB::raw('CASE
                        WHEN vendors.transaction_type = "inbound" THEN CONCAT("SO/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0"))
                        WHEN vendors.transaction_type = "outbound" THEN CONCAT("PO/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0"))
                        ELSE CONCAT("ORD/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0"))
                    END as kodeOrder')
        )
        ->get();

    if ($activities->isEmpty()) {
        return response()->json(['message' => 'No activities found for this order.', 'status' => 404], 404);
    }

    return response()->json(['data' => $activities, 'status' => 200, 'message' => 'Processing activities by Order retrieved successfully.']);
}


    public function getActivitiesByProduct($product_id)
    {
        $activities = DB::table('manufacturer_processing_activities')
            ->join('orders', 'manufacturer_processing_activities.order_id', '=', 'orders.id')
            ->join('products', 'manufacturer_processing_activities.product_id', '=', 'products.id')
            ->where('manufacturer_processing_activities.product_id', $product_id)
            ->select('manufacturer_processing_activities.*', 'orders.order_status as order_status', 'products.name as product_name', 'products.description as product_description')
            ->get();

        if ($activities->isEmpty()) {
            return response()->json(['message' => 'No activities found for this product.', 'status' => 404], 404);
        }
        return response()->json(['data' => $activities, 'status' => 200, 'message' => 'Processing activities by Product retrieved successfully.']);
    }
}