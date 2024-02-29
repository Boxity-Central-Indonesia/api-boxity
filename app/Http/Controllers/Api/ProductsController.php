<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['warehouse', 'category', 'prices'])->get()->map(function ($product) {
            $product->price = (int) $product->price;
            return $product;
        });
        return response()->json([
            'status' => 200,
            'data' => $products,
            'message' => 'Products retrieved successfully.',
        ]);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $product,
            'message' => 'Product created successfully.',
        ], 201);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json([
            'status' => 201,
            'data' => $product,
            'message' => 'Product updated successfully.',
        ]);
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

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully.',
        ]);
    }
    public function processingActivities($productId)
    {
        $product = Product::with('processingActivities')->find($productId);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $product->processingActivities,
            'message' => 'Processing activities for product retrieved successfully.',
        ]);
    }
}
