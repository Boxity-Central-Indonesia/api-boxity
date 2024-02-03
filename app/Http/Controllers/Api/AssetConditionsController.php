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
        $conditions = AssetCondition::all();
        return response()->json([
            'status' => 200,
            'data' => $conditions,
            'message' => 'Asset conditions retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'condition' => 'required|string|max:255',
        ]);

        $condition = AssetCondition::create($validated);
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'condition' => 'required|string|max:255',
        ]);

        $condition = AssetCondition::findOrFail($id);
        $condition->update($validated);
        return response()->json([
            'status' => 200,
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
