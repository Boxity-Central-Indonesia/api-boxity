<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AssetConditionRequest;
use App\Models\AssetCondition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class AssetConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $conditions = AssetCondition::all();
        return response()->json([
            'status' => 200,
            'data' => $conditions,
            'message' => 'Asset conditions retrieved successfully.',
        ]);
    }

    public function store(AssetConditionRequest $request)
    {
        $condition = AssetCondition::create($request->validated());
        broadcast(new formCreated('New Asset condition created successfully.'));
        
        return response()->json([
            'status' => 201,
            'data' => $condition,
            'message' => 'Asset condition created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $condition = AssetCondition::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $condition,
            'message' => 'Asset condition retrieved successfully.',
        ]);
    }

    public function update(AssetConditionRequest $request, $id)
    {
        $condition = AssetCondition::findOrFail($id);
        $condition->update($request->validated());
        broadcast(new formCreated('Asset condition updated successfully.'));
        
        return response()->json([
            'status' => 201,
            'data' => $condition,
            'message' => 'Asset condition updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $condition = AssetCondition::findOrFail($id);
        $condition->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Asset condition deleted successfully.',
        ]);
    }
}
