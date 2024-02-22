<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProcessingActivityRequest;
use App\Models\Order;
use App\Models\ProcessingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class ProcessingActivityController extends Controller
{
    public function index()
    {
        // Ubah 'carcass' menjadi 'product' untuk memuat relasi produk
        $activities = ProcessingActivity::all();
        return response()->json([
            'status' => 200,
            'data' => $activities,
            'message' => 'All processing activities retrieved successfully.',
        ]);
    }

    public function store(ProcessingActivityRequest $request)
    {
        $validated = $request->validated();

        // Pastikan untuk menghapus referensi ke 'carcass_id' dalam validasi dan pembuatan
        $activity = ProcessingActivity::create([
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'],
            'activity_type' => $validated['activity_type'],
            'activity_date' => Date::now(),
            'status_activities' => 'In Production',
            'details' => $validated['details'],
        ]);
        // Cek dan tandai order sebagai completed jika memenuhi kriteria
        $this->markOrderAsCompleted($validated['order_id']);
        return response()->json([
            'status' => 201,
            'data' => $activity,
            'message' => 'Processing activity created successfully.',
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

    public function update(ProcessingActivityRequest $request, $id)
    {
        $activity = ProcessingActivity::findOrFail($id);

        $validated = $request->validated();

        // Pastikan untuk menghapus referensi ke 'carcass_id' dalam pembaruan
        $activity->update($validated);
        return response()->json([
            'status' => 200,
            'data' => $activity,
            'message' => 'Processing activity updated successfully.',
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
