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
    public function index(Account $account)
    {
        $transactions = $account->transactions()->get();

        return response()->json([
            'data' => $transactions,
            'message' => 'Transactions retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created transaction for an account.
     *
     * @param Request $request
     * @param Account $account
     * @return JsonResponse
     */
    public function store(Request $request, Account $account)
    {
        $validationRules = [
            'type' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ];

        $customMessages = [
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'date.required' => 'The date field is required.',
            'date.date' => 'Please provide a valid date.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $transaction = $account->transactions()->create($request->all());

        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction created successfully.',
        ], 201);
    }

    /**
     * Display the specified transaction.
     *
     * @param Account $account
     * @param AccountsTransaction $transaction
     * @return JsonResponse
     */
    public function show(Account $account, AccountsTransaction $transaction)
    {
        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified transaction.
     *
     * @param Request $request
     * @param Account $account
     * @param AccountsTransaction $transaction
     * @return JsonResponse
     */
    public function update(Request $request, Account $account, AccountsTransaction $transaction)
    {
        $validationRules = [
            'type' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ];

        $customMessages = [
            'type.required' => 'The type field is required.',
            'type.string' => 'The type must be a string.',
            'date.required' => 'The date field is required.',
            'date.date' => 'Please provide a valid date.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $transaction->update($request->all());

        return response()->json([
            'data' => $transaction,
            'message' => 'Transaction updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified transaction from storage.
     *
     * @param Account $account
     * @param AccountsTransaction $transaction
     * @return JsonResponse
     */
    public function destroy(Account $account, AccountsTransaction $transaction)
    {
        $transaction->delete();

        return response()->json(null, 204);
    }
}
