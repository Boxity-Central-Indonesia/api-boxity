<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductsPriceRequest;
use App\Models\Product;
use App\Models\ProductsPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class ProductsPricesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prices = ProductsPrice::with('product')->get();
        return response()->json([
            'status' => 200,
            'data' => $prices,
            'message' => 'Product prices retrieved successfully.',
        ]);
    }

    public function store(ProductsPriceRequest $request)
    {
        $price = ProductsPrice::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $price,
            'message' => 'Product price created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $price = ProductsPrice::with('product')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $price,
            'message' => 'Product price retrieved successfully.',
        ]);
    }

    public function update(ProductsPriceRequest $request, $id)
    {
        $price = ProductsPrice::findOrFail($id);
        $price->update($request->all());
        return response()->json([
            'status' => 201,
            'data' => $price,
            'message' => 'Product price updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $price = ProductsPrice::findOrFail($id);
        $price->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product price deleted successfully.',
        ]);
    }
}
