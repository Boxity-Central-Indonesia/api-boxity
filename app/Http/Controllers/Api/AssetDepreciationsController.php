<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AssetDepreciationRequest;
use App\Models\Asset;
use App\Models\AssetDepreciation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class AssetDepreciationsController extends Controller
{
    /**
     * Display the depreciation details for a given asset.
     *
     * @param Asset $asset
     * @return JsonResponse
     */
    public function index()
    {
        $depreciations = AssetDepreciation::with('asset')->get();
        return response()->json([
            'status' => 200,
            'data' => $depreciations,
            'message' => 'Asset depreciations retrieved successfully.',
        ]);
    }

    public function store(AssetDepreciationRequest $request)
    {
        $depreciation = AssetDepreciation::create($request->validated());
        return response()->json([
            'status' => 201,
            'data' => $depreciation,
            'message' => 'Asset depreciation created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $depreciation = AssetDepreciation::with('asset')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $depreciation,
            'message' => 'Asset depreciation retrieved successfully.',
        ]);
    }

    public function update(AssetDepreciationRequest $request, $id)
    {
        $depreciation = AssetDepreciation::findOrFail($id);
        $depreciation->update($request->validated());
        return response()->json([
            'status' => 200,
            'data' => $depreciation,
            'message' => 'Asset depreciation updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        AssetDepreciation::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Asset depreciation deleted successfully.',
        ]);
    }
}
