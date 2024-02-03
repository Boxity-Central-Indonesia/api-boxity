<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Models\AccountsTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AccountsTransactionsController extends Controller
{
    /**
     * Display a listing of the transactions for a given account.
     *
     * @param Account $account
     * @return JsonResponse
     */
    public function index()
    {
        $transactions = AccountsTransaction::with('account')->get();
        return response()->json([
            'status' => 200,
            'data' => $transactions,
            'message' => 'Accounts transactions retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $transaction = AccountsTransaction::create($validated);
        return response()->json([
            'status' => 201,
            'data' => $transaction,
            'message' => 'Accounts transaction created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $transaction = AccountsTransaction::with('account')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $transaction,
            'message' => 'Accounts transaction retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $transaction = AccountsTransaction::findOrFail($id);
        $transaction->update($validated);
        return response()->json([
            'status' => 200,
            'data' => $transaction,
            'message' => 'Accounts transaction updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        AccountsTransaction::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Accounts transaction deleted successfully.',
        ]);
    }
}
