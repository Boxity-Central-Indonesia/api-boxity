<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProcessingActivityRequest;
use App\Models\Order;
use App\Models\ProcessingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\Events\formCreated;

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
            ->where('manufacturer_processing_activities.order_id', $order_id)
            ->select(
                'manufacturer_processing_activities.*',
                'orders.order_status as order_status',
                'products.name as product_name',
                'products.description as product_description',
                DB::raw('CONCAT("ORD/", DATE_FORMAT(orders.created_at, "%Y"), "/", DATE_FORMAT(orders.created_at, "%m"), "/", LPAD(orders.id, 4, "0")) as kodeOrder')
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
