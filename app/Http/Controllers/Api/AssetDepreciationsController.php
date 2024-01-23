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

        $customMessages = [
            'method.required' => 'The method field is required.',
            'method.in' => 'The selected method is not valid. Please choose one of: linear, declining_balance, sum_of_the_years_digits, units_of_production, double_declining_balance.',
            'useful_life.required' => 'The useful life field is required.',
            'useful_life.integer' => 'The useful life must be an integer.',
            'useful_life.min' => 'The useful life must be at least 1.',
            'residual_value.required' => 'The residual value field is required.',
            'residual_value.numeric' => 'The residual value must be a number.',
            'residual_value.min' => 'The residual value must be at least 0.',
            'start_date.required' => 'The start date field is required.',
            'start_date.date' => 'Please provide a valid start date.',
            'current_value.required' => 'The current value field is required.',
            'current_value.numeric' => 'The current value must be a number.',
            'current_value.min' => 'The current value must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

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
            'method' => 'required|in:linear,declining_balance,sum_of_the_years_digits,units_of_production,double_declining_balance',
            'useful_life' => 'required|integer|min:1',
            'residual_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'current_value' => 'required|numeric|min:0',
        ];

        $customMessages = [
            'method.required' => 'The method field is required.',
            'method.in' => 'The selected method is not valid. Please choose one of: linear, declining_balance, sum_of_the_years_digits, units_of_production, double_declining_balance.',
            'useful_life.required' => 'The useful life field is required.',
            'useful_life.integer' => 'The useful life must be an integer.',
            'useful_life.min' => 'The useful life must be at least 1.',
            'residual_value.required' => 'The residual value field is required.',
            'residual_value.numeric' => 'The residual value must be a number.',
            'residual_value.min' => 'The residual value must be at least 0.',
            'start_date.required' => 'The start date field is required.',
            'start_date.date' => 'Please provide a valid start date.',
            'current_value.required' => 'The current value field is required.',
            'current_value.numeric' => 'The current value must be a number.',
            'current_value.min' => 'The current value must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

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
