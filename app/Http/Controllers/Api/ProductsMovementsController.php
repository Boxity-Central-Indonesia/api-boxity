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
            'data' => $movements,
            'message' => 'Products movements retrieved successfully.',
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
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_type' => 'required|in:purchase,sale,transfer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];

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

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $movement = ProductsMovement::create($request->all());

        // Update stock balance based on movement type
        $product = $movement->product;
        switch ($movement->movement_type) {
            case 'purchase':
                $product->stock += $movement->quantity;
                break;
            case 'sale':
                $product->stock -= $movement->quantity;
                break;
            case 'transfer':
                // Handle transfer logic, if needed
                break;
        }
        $product->save();

        return response()->json([
            'data' => $movement,
            'message' => 'Product movement created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductsMovement  $movement
     * @return \Illuminate\Http\Response
     */
    public function show(ProductsMovement $movement)
    {
        return response()->json([
            'data' => $movement->load(['product', 'warehouse']),
            'message' => 'Product movement retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductsMovement  $movement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductsMovement $movement)
    {
        $validationRules = [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_type' => 'required|in:purchase,sale,transfer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];

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

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $movement->update($request->all());

        // Update stock balance if movement type or quantity changed
        if ($movement->isDirty('movement_type') || $movement->isDirty('quantity')) {
            $product = $movement->product;
            $originalQuantity = $movement->getOriginal('quantity');
            switch ($movement->movement_type) {
                case 'purchase':
                    $product->stock += ($movement->quantity - $originalQuantity);
                    break;
                case 'sale':
                    $product->stock -= ($movement->quantity - $originalQuantity);
                    break;
                case 'transfer':
                    // Handle transfer logic, if needed
                    break;
            }
            $product->save();
        }

        return response()->json([
            'data' => $movement,
            'message' => 'Product movement updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductsMovement  $movement
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductsMovement $movement)
    {
        $movement->delete();

        return response()->json(null, 204);
    }

    /**
     * Get the products movements for a specific product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function forProduct(Product $product)
    {
        $movements = ProductsMovement::where('product_id', $product->id)->get();

        return response()->json([
            'data' => $movements,
            'message' => 'Products movements for product retrieved successfully.',
        ]);
    }

    /**
     * Get the products movements for a specific warehouse.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function forWarehouse(Warehouse $warehouse)
    {
        $movements = ProductsMovement::where('warehouse_id', $warehouse->id)->get();

        return response()->json([
            'data' => $movements,
            'message' => 'Products movements for warehouse retrieved successfully.',
        ]);
    }
}
