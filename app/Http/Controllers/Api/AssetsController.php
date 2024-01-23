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

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'code.required' => 'The code field is required.',
            'code.string' => 'The code must be a string.',
            'code.unique' => 'The entered code is already in use.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be one of: tangible, intangible.',
            'acquisition_date.required' => 'The acquisition date field is required.',
            'acquisition_date.date' => 'Please provide a valid acquisition date.',
            'acquisition_cost.required' => 'The acquisition cost field is required.',
            'acquisition_cost.numeric' => 'The acquisition cost must be a number.',
            'acquisition_cost.min' => 'The acquisition cost must be at least 0.',
            'book_value.required' => 'The book value field is required.',
            'book_value.numeric' => 'The book value must be a number.',
            'book_value.min' => 'The book value must be at least 0.',
            'location_id.integer' => 'The location ID must be an integer.',
            'location_id.exists' => 'The selected location ID does not exist in the database.',
            'condition_id.integer' => 'The condition ID must be an integer.',
            'condition_id.exists' => 'The selected condition ID does not exist in the database.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

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
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:assets,code',
            'type' => 'required|in:tangible,intangible',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric|min:0',
            'book_value' => 'required|numeric|min:0',
            'location_id' => 'nullable|integer|exists:asset_locations,id',
            'condition_id' => 'nullable|integer|exists:asset_conditions,id',
        ];

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'code.required' => 'The code field is required.',
            'code.string' => 'The code must be a string.',
            'code.unique' => 'The entered code is already in use.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be one of: tangible, intangible.',
            'acquisition_date.required' => 'The acquisition date field is required.',
            'acquisition_date.date' => 'Please provide a valid acquisition date.',
            'acquisition_cost.required' => 'The acquisition cost field is required.',
            'acquisition_cost.numeric' => 'The acquisition cost must be a number.',
            'acquisition_cost.min' => 'The acquisition cost must be at least 0.',
            'book_value.required' => 'The book value field is required.',
            'book_value.numeric' => 'The book value must be a number.',
            'book_value.min' => 'The book value must be at least 0.',
            'location_id.integer' => 'The location ID must be an integer.',
            'location_id.exists' => 'The selected location ID does not exist in the database.',
            'condition_id.integer' => 'The condition ID must be an integer.',
            'condition_id.exists' => 'The selected condition ID does not exist in the database.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

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
