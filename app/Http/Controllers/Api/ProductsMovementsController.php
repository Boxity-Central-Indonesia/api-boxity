<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductsMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductsMovementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movements = ProductsMovement::with(['product', 'warehouse'])->get();
        return response()->json([
            'status' => 200,
            'data' => $movements,
            'message' => 'Product movements retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $customMessages = [
            'product_id.required' => 'The product ID field is required.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'warehouse_id.required' => 'The warehouse ID field is required.',
            'warehouse_id.exists' => 'The selected warehouse ID does not exist in the database.',
            'movement_type.required' => 'The movement type field is required.',
            'movement_type.in' => 'The movement type must be one of: purchase, sale, transfer.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 1.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
        ];

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_type' => 'required|in:purchase,sale,transfer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

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

    public function update(Request $request, $id)
    {
        $customMessages = [
            'product_id.required' => 'The product ID field is required.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'warehouse_id.required' => 'The warehouse ID field is required.',
            'warehouse_id.exists' => 'The selected warehouse ID does not exist in the database.',
            'movement_type.required' => 'The movement type field is required.',
            'movement_type.in' => 'The movement type must be one of: purchase, sale, transfer.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 1.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
        ];
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_type' => 'required|in:purchase,sale,transfer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $movement = ProductsMovement::findOrFail($id);
        $movement->update($request->all());
        return response()->json([
            'status' => 200,
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
