<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $assets = Asset::with(['location', 'condition'])->get();
        return response()->json([
            'status' => 200,
            'data' => $assets,
            'message' => 'Assets retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:assets',
            'type' => 'required|in:tangible,intangible',
            'description' => 'nullable|string',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric',
            'book_value' => 'required|numeric',
            'location_id' => 'nullable|exists:asset_locations,id',
            'condition_id' => 'nullable|exists:asset_conditions,id',
        ]);

        $asset = Asset::create($validated);
        return response()->json([
            'status' => 201,
            'data' => $asset,
            'message' => 'Asset created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $asset = Asset::with(['location', 'condition'])->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $asset,
            'message' => 'Asset retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:assets,code,' . $id,
            'type' => 'required|in:tangible,intangible',
            'description' => 'nullable|string',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric',
            'book_value' => 'required|numeric',
            'location_id' => 'nullable|exists:asset_locations,id',
            'condition_id' => 'nullable|exists:asset_conditions,id',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update($validated);
        return response()->json([
            'status' => 200,
            'data' => $asset,
            'message' => 'Asset updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        Asset::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Asset deleted successfully.',
        ]);
    }
}
