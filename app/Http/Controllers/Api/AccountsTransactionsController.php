<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountsTransactionRequest;
use App\Models\Account;
use App\Models\AccountsTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

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
        $transactions = AccountsTransaction::with('account')->get()->map(function ($transactions) {
            $transactions->amount = (int) $transactions->amount;
            $transactions->account->balance = (int) $transactions->account->balance;
            return $transactions;
        });
        return response()->json([
            'status' => 200,
            'data' => $transactions,
            'message' => 'Accounts transactions retrieved successfully.',
        ]);
    }

    public function store(AccountsTransactionRequest $request)
    {
        $validated = $request->validated();
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

    public function update(AccountsTransactionRequest $request, $id)
    {
        $transaction = AccountsTransaction::findOrFail($id);
        $validated = $request->validated();
        $transaction->update($validated);
        return response()->json([
            'status' => 201,
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
