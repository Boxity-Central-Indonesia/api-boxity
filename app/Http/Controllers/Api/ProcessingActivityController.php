<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProcessingActivityRequest;
use App\Models\ProcessingActivity;
use Illuminate\Http\Request;

class ProcessingActivityController extends Controller
{
    public function index()
    {
        $activities = ProcessingActivity::with('carcass')->get();
        return response()->json([
            'status' => 200,
            'data' => $activities,
            'message' => 'All processing activities retrieved successfully.',
        ]);
    }
    public function getActivitiesByCarcass($carcass_id)
    {
        // Mengambil data aktivitas dengan informasi produk dan slaughter yang terkait
        $groupedActivities = ProcessingActivity::with(['carcass.slaughtering.product'])
            ->whereHas('carcass', function ($query) use ($carcass_id) {
                $query->where('id', $carcass_id);
            })
            ->get();

        if ($groupedActivities->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No processing activities found for the specified carcass ID.',
            ], 404);
        }

        $groupedData = [];
        foreach ($groupedActivities as $activity) {
            $slaughteringInfo = $activity->carcass->slaughtering;
            $productInfo = $slaughteringInfo->product;

            $groupedData[] = [
                'slaughtering_id' => $slaughteringInfo->id,
                'slaughtering_info' => $slaughteringInfo,
                'product_info' => $productInfo,
                'activity' => $activity,
            ];
        }

        return response()->json([
            'status' => 200,
            'data' => $groupedData,
            'message' => 'Processing activities grouped by slaughtering ID retrieved successfully.'
        ]);
    }

    public function store(ProcessingActivityRequest $request)
    {
        $validated = $request->validated();

        $activity = ProcessingActivity::create($validated);
        return response()->json([
            'status' => 201,
            'data' => $activity,
            'message' => 'Processing activity created successfully.',
        ]);
    }

    public function show($id)
    {
        $activity = ProcessingActivity::with('carcass')->findOrFail($id);
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
}
