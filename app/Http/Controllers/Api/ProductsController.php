<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['warehouse', 'category'])->get();
        return response()->json([
            'status' => 200,
            'data' => $products,
            'message' => 'Products retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:products_categories,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $product = Product::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $product,
            'message' => 'Product created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $product = Product::with(['warehouse', 'category'])->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $product,
            'message' => 'Product retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:products_categories,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json([
            'status' => 200,
            'data' => $product,
            'message' => 'Product updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
