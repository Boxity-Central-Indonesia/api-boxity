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

        $validator = Validator::make($request->all(), $validationRules);

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
            'amount' => 'sometimes|numeric|min:0',
            'product_id' => 'sometimes|integer|exists:products,id',
            'unit_price' => 'sometimes|numeric|min:0',
            'total_price' => 'sometimes|numeric|min:0',
            'taxes' => 'sometimes|numeric|min:0',
            'shipping_cost' => 'sometimes|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $validationRules);

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
