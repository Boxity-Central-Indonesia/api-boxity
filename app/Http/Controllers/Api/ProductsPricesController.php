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
        $prices = ProductsPrice::with('product')->get();
        return response()->json([
            'status' => 200,
            'data' => $prices,
            'message' => 'Product prices retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'selling_price' => 'required|numeric',
            'buying_price' => 'required|numeric',
            'discount_price' => 'required|numeric',
        ]);

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

    public function update(Request $request, $id)
    {
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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'selling_price' => 'required|numeric',
            'buying_price' => 'required|numeric',
            'discount_price' => 'required|numeric',
        ]);

        $price = ProductsPrice::findOrFail($id);
        $price->update($request->all());
        return response()->json([
            'status' => 200,
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
