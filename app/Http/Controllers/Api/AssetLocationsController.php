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
        $locations = AssetLocation::all();
        return response()->json([
            'status' => 200,
            'data' => $locations,
            'message' => 'Asset locations retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $location = AssetLocation::create($validated);
        return response()->json([
            'status' => 201,
            'data' => $location,
            'message' => 'Asset location created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $location = AssetLocation::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $location,
            'message' => 'Asset location retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $location = AssetLocation::findOrFail($id);
        $location->update($validated);
        return response()->json([
            'status' => 200,
            'data' => $location,
            'message' => 'Asset location updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        AssetLocation::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Asset location deleted successfully.',
        ]);
    }
}
