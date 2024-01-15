<?php

namespace App\Http\Controllers\Api;

use App\Models\AssetLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AssetLocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $locations = AssetLocation::with('assets')->get();

        return response()->json([
            'data' => $locations,
            'message' => 'Asset locations retrieved successfully.',
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
            'address' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $location = AssetLocation::create($request->all());

        return response()->json([
            'data' => $location,
            'message' => 'Asset location created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param AssetLocation $location
     * @return JsonResponse
     */
    public function show(AssetLocation $location)
    {
        return response()->json([
            'data' => $location,
            'message' => 'Asset location retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param AssetLocation $location
     * @return JsonResponse
     */
    public function update(Request $request, AssetLocation $location)
    {
        $validationRules = [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $location->update($request->all());

        return response()->json([
            'data' => $location,
            'message' => 'Asset location updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AssetLocation $location
     * @return JsonResponse
     */
    public function destroy(AssetLocation $location)
    {
        $location->delete();

        return response()->json(null, 204);
    }
}
