<?php

namespace App\Http\Controllers\Api;

use App\Models\VendorTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;
use App\Models\VendorContact;

class VendorTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $transactions = VendorTransaction::with(['vendor', 'product', 'order'])->get()->map(function ($transactions) {
            $transactions->amount = (int) $transactions->amount;
            $transactions->unit_price = (int) $transactions->unit_price;
            $transactions->total_price = (int) $transactions->total_price;
            $transactions->taxes = (int) $transactions->taxes;
            $transactions->shipping_cost = (int) $transactions->shipping_cost;
            return $transactions;
        });;
        return response()->json([
            'status' => 200,
            'data' => $transactions,
            'message' => 'Vendor transactions retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendors_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric',
            'product_id' => 'nullable|exists:products,id',
            'unit_price' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
        ]);

        $transaction = VendorTransaction::create($validated);
        broadcast(new formCreated('New Vendor transaction created successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $transaction,
            'message' => 'Vendor transaction created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $transaction = VendorTransaction::with(['vendor', 'product'])->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $transaction,
            'message' => 'Vendor transaction retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vendors_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric',
            'product_id' => 'nullable|exists:products,id',
            'unit_price' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
        ]);

        $transaction = VendorTransaction::findOrFail($id);
        $transaction->update($validated);
        broadcast(new formCreated('Vendor transaction updated successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $transaction,
            'message' => 'Vendor transaction updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        VendorTransaction::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Vendor transaction deleted successfully.',
        ]);
    }
}
