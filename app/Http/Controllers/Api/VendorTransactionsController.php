<?php

namespace App\Http\Controllers\Api;

use App\Models\VendorTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VendorTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $transactions = VendorTransaction::with(['vendor', 'product'])->get();

        return response()->json([
            'data' => $transactions,
            'message' => 'Vendor transactions retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validationRules = [
            'vendors_id' => 'required|integer|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'product_id' => 'nullable|integer|exists:products,id',
            'unit_price' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
        ];

        $customMessages = [
            'vendors_id.required' => 'The vendors ID field is required.',
            'vendors_id.integer' => 'The vendors ID must be an integer.',
            'vendors_id.exists' => 'The selected vendors ID does not exist in the database.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'product_id.integer' => 'The product ID must be an integer.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.min' => 'The unit price must be at least 0.',
            'total_price.numeric' => 'The total price must be a number.',
            'total_price.min' => 'The total price must be at least 0.',
            'taxes.numeric' => 'The taxes must be a number.',
            'taxes.min' => 'The taxes must be at least 0.',
            'shipping_cost.numeric' => 'The shipping cost must be a number.',
            'shipping_cost.min' => 'The shipping cost must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $transaction = VendorTransaction::create($request->all());

        return response()->json([
            'data' => $transaction,
            'message' => 'Vendor transaction created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param VendorTransaction $transaction
     * @return JsonResponse
     */
    public function show(VendorTransaction $transaction)
    {
        return response()->json([
            'data' => $transaction,
            'message' => 'Vendor transaction retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param VendorTransaction $transaction
     * @return JsonResponse
     */
    public function update(Request $request, VendorTransaction $transaction)
    {
        $validationRules = [
            'vendors_id' => 'required|integer|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'product_id' => 'nullable|integer|exists:products,id',
            'unit_price' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
        ];

        $customMessages = [
            'vendors_id.required' => 'The vendors ID field is required.',
            'vendors_id.integer' => 'The vendors ID must be an integer.',
            'vendors_id.exists' => 'The selected vendors ID does not exist in the database.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'product_id.integer' => 'The product ID must be an integer.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'unit_price.numeric' => 'The unit price must be a number.',
            'unit_price.min' => 'The unit price must be at least 0.',
            'total_price.numeric' => 'The total price must be a number.',
            'total_price.min' => 'The total price must be at least 0.',
            'taxes.numeric' => 'The taxes must be a number.',
            'taxes.min' => 'The taxes must be at least 0.',
            'shipping_cost.numeric' => 'The shipping cost must be a number.',
            'shipping_cost.min' => 'The shipping cost must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $transaction->update($request->all());

        return response()->json([
            'data' => $transaction,
            'message' => 'Vendor transaction updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param VendorTransaction $transaction
     * @return JsonResponse
     */
    public function destroy(VendorTransaction $transaction)
    {
        $transaction->delete();

        return response()->json(null, 204);
    }
}
