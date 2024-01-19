<?php

namespace App\Http\Controllers\Api;

use App\Models\AssetCondition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AssetConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $conditions = AssetCondition::with('assets')->get();

        return response()->json([
            'data' => $conditions,
            'message' => 'Asset conditions retrieved successfully.',
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
            'condition' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $condition = AssetCondition::create($request->all());

        return response()->json([
            'data' => $condition,
            'message' => 'Asset condition created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param AssetCondition $condition
     * @return JsonResponse
     */
    public function show(AssetCondition $condition)
    {
        return response()->json([
            'data' => $condition,
            'message' => 'Asset condition retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param AssetCondition $condition
     * @return JsonResponse
     */
    public function update(Request $request, AssetCondition $condition)
    {
        $validationRules = [
            'condition' => 'sometimes|string|max:255',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $condition->update($request->all());

        return response()->json([
            'data' => $condition,
            'message' => 'Asset condition updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AssetCondition $condition
     * @return JsonResponse
     */
    public function destroy(AssetCondition $condition)
    {
        $condition->delete();

        return response()->json(null, 204);
    }
}
