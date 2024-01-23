<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductsPrice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductsPricesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $prices = ProductsPrice::all();

        return response()->json([
            'data' => $prices,
            'message' => 'Products prices retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'product_id' => 'required|exists:products,id',
            'selling_price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
        ];

        $customMessages = [
            'product_id.required' => 'The product ID field is required.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'selling_price.required' => 'The selling price field is required.',
            'selling_price.numeric' => 'The selling price must be a number.',
            'selling_price.min' => 'The selling price must be at least 0.',
            'buying_price.required' => 'The buying price field is required.',
            'buying_price.numeric' => 'The buying price must be a number.',
            'buying_price.min' => 'The buying price must be at least 0.',
            'discount_price.numeric' => 'The discount price must be a number.',
            'discount_price.min' => 'The discount price must be at least 0.',
        ];
        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $price = ProductsPrice::create($request->all());

        return response()->json([
            'data' => $price,
            'message' => 'Product price created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductsPrice  $price
     * @return \Illuminate\Http\Response
     */
    public function show(ProductsPrice $price)
    {
        return response()->json([
            'data' => $price,
            'message' => 'Product price retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductsPrice  $price
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductsPrice $price)
    {
        $validationRules = [
            'product_id' => 'required|exists:products,id',
            'selling_price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
        ];

        $customMessages = [
            'product_id.required' => 'The product ID field is required.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'selling_price.required' => 'The selling price field is required.',
            'selling_price.numeric' => 'The selling price must be a number.',
            'selling_price.min' => 'The selling price must be at least 0.',
            'buying_price.required' => 'The buying price field is required.',
            'buying_price.numeric' => 'The buying price must be a number.',
            'buying_price.min' => 'The buying price must be at least 0.',
            'discount_price.numeric' => 'The discount price must be a number.',
            'discount_price.min' => 'The discount price must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $price->update($request->all());

        return response()->json([
            'data' => $price,
            'message' => 'Product price updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductsPrice  $price
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductsPrice $price)
    {
        $price->delete();

        return response()->json(null, 204);
    }
}
