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
        $assets = Asset::with(['location', 'condition', 'depreciation'])->get();

        return response()->json([
            'data' => $assets,
            'message' => 'Assets retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:assets,code',
            'type' => 'required|in:tangible,intangible',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'book_value' => 'required|numeric|min:0',
            'location_id' => 'nullable|integer|exists:asset_locations,id',
            'condition_id' => 'nullable|integer|exists:asset_conditions,id',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $asset = Asset::create($request->all());

        return response()->json([
            'data' => $asset,
            'message' => 'Asset created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Asset $asset
     * @return JsonResponse
     */
    public function show(Asset $asset)
    {
        return response()->json([
            'data' => $asset->load(['location', 'condition', 'depreciation']),
            'message' => 'Asset retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Asset $asset
     * @return JsonResponse
     */
    public function update(Request $request, Asset $asset)
    {
        $validationRules = [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:assets,code,' . $asset->id,
            'type' => 'sometimes|in:tangible,intangible',
            'description' => 'nullable|string',
            'acquisition_date' => 'sometimes|date',
            'acquisition_cost' => 'sometimes|numeric|min:0',
            'book_value' => 'sometimes|numeric|min:0',
            'location_id' => 'nullable|integer|exists:asset_locations,id',
            'condition_id' => 'nullable|integer|exists:asset_conditions,id',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $asset->update($request->all());

        return response()->json([
            'data' => $asset,
            'message' => 'Asset updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Asset $asset
     * @return JsonResponse
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();

        return response()->json(null, 204);
    }
}
