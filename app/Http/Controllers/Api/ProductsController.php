<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $products = Product::with(['warehouse', 'category', 'prices'])
            ->get()
            ->map(function ($product) {
                $product->price = (int) $product->price;
                return $product;
            });

        return response()->json([
            'status' => 200,
            'data' => $products,
            'message' => 'Products retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $product = Product::create($request->all());

        return response()->json([
            'status' => 201,
            'data' => $product,
            'message' => 'Product created successfully.',
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return response()->json([
            'status' => 201,
            'data' => $product,
            'message' => 'Product updated successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $product = Product::with(['warehouse', 'category'])->findOrFail($id);

        return response()->json([
            'status' => 200,
            'data' => $product,
            'message' => 'Product retrieved successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
   