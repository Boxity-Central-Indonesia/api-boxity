<?php

namespace App\Http\Controllers\Api;

use App\Models\AccountsBalance;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AccountsBalancesController extends Controller
{
    /**
     * Display a listing of the balances for a given account.
     *
     * @param Account $account
     * @return JsonResponse
     */
    public function index(Account $account)
    {
        $balances = $account->balances()->get();

        return response()->json([
            'data' => $balances,
            'message' => 'Balances retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created balance for an account.
     *
     * @param Request $request
     * @param Account $account
     * @return JsonResponse
     */
    public function store(Request $request, Account $account)
    {
        $validationRules = [
            'date' => 'required|date',
            'balance' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $balance = $account->balances()->create($request->all());

        return response()->json([
            'data' => $balance,
            'message' => 'Balance created successfully.',
        ], 201);
    }

    /**
     * Display the specified balance.
     *
     * @param Account $account
     * @param AccountsBalance $balance
     * @return JsonResponse
     */
    public function show(Account $account, AccountsBalance $balance)
    {
        return response()->json([
            'data' => $balance,
            'message' => 'Balance retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified balance.
     *
     * @param Request $request
     * @param Account $account
     * @param AccountsBalance $balance
     * @return JsonResponse
     */
    public function update(Request $request, Account $account, AccountsBalance $balance)
    {
        $validationRules = [
            'date' => 'sometimes|date',
            'balance' => 'sometimes|numeric',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $balance->update($request->all());

        return response()->json([
            'data' => $balance,
            'message' => 'Balance updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified balance from storage.
     *
     * @param Account $account
     * @param AccountsBalance $balance
     * @return JsonResponse
     */
    public function destroy(Account $account, AccountsBalance $balance)
    {
        $balance->delete();

        return response()->json(null, 204);
    }
}
