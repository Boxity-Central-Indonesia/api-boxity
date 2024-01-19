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
    public function show(Asset $asset)
    {
        $depreciation = $asset->depreciation;

        return response()->json([
            'data' => $depreciation,
            'message' => 'Asset depreciation retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created depreciation for an asset.
     *
     * @param Request $request
     * @param Asset $asset
     * @return JsonResponse
     */
    public function store(Request $request, Asset $asset)
    {
        $validationRules = [
            'method' => 'required|in:linear,declining_balance,sum_of_the_years_digits,units_of_production,double_declining_balance',
            'useful_life' => 'required|integer|min:1',
            'residual_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'current_value' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $depreciation = $asset->depreciation()->create($request->all());

        return response()->json([
            'data' => $depreciation,
            'message' => 'Asset depreciation created successfully.',
        ], 201);
    }

    /**
     * Update the depreciation of an asset.
     *
     * @param Request $request
     * @param Asset $asset
     * @return JsonResponse
     */
    public function update(Request $request, Asset $asset)
    {
        $validationRules = [
            'method' => 'sometimes|in:linear,declining_balance,sum_of_the_years_digits,units_of_production,double_declining_balance',
            'useful_life' => 'sometimes|integer|min:1',
            'residual_value' => 'sometimes|numeric|min:0',
            'start_date' => 'sometimes|date',
            'current_value' => 'sometimes|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $asset->depreciation->update($request->all());

        return response()->json([
            'data' => $asset->depreciation,
            'message' => 'Asset depreciation updated successfully.',
        ], 200);
    }
}
