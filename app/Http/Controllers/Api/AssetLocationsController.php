<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AssetLocationRequest;
use App\Models\AssetLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

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

    public function store(AssetLocationRequest $request)
    {
        $location = AssetLocation::create($request->validated());
        broadcast(new formCreated('New Asset location created successfully.'));
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

    public function update(AssetLocationRequest $request, $id)
    {
        $location = AssetLocation::findOrFail($id);
        $location->update($request->validated());
        broadcast(new formCreated('New Asset location created successfully.'));
        return response()->json([
            'status' => 201,
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
