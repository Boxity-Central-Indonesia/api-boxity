<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductsMovementRequest;
use App\Models\Product;
use App\Models\ProductsMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class ProductsMovementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movements = ProductsMovement::with(['product', 'warehouse'])->get()->map(function ($movements) {
            $movements->price = (int) $movements->price;
            return $movements;
        });
        return response()->json([
            'status' => 200,
            'data' => $movements,
            'message' => 'Product movements retrieved successfully.',
        ]);
    }

    public function store(ProductsMovementRequest $request)
    {

        $movement = ProductsMovement::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $movement,
            'message' => 'Product movement created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $movement = ProductsMovement::with(['product', 'warehouse'])->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $movement,
            'message' => 'Product movement retrieved successfully.',
        ]);
    }

    public function update(ProductsMovementRequest $request, $id)
    {
        $movement = ProductsMovement::findOrFail($id);
        $movement->update($request->all());
        return response()->json([
            'status' => 201,
            'data' => $movement,
            'message' => 'Product movement updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $movement = ProductsMovement::findOrFail($id);
        $movement->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product movement deleted successfully.',
        ]);
    }
}
