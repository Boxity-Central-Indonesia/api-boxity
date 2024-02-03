<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'method' => 'required|in:linear,declining_balance,sum_of_the_years_digits,units_of_production,double_declining_balance',
            'useful_life' => 'required|integer',
            'residual_value' => 'required|numeric',
            'start_date' => 'required|date',
            'current_value' => 'required|numeric',
        ]);

        $depreciation = AssetDepreciation::create($validated);
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'method' => 'required|in:linear,declining_balance,sum_of_the_years_digits,units_of_production,double_declining_balance',
            'useful_life' => 'required|integer',
            'residual_value' => 'required|numeric',
            'start_date' => 'required|date',
            'current_value' => 'required|numeric',
        ]);

        $depreciation = AssetDepreciation::findOrFail($id);
        $depreciation->update($validated);
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
