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
    public function index()
    {
        $balances = AccountsBalance::with('account')->get()->map(function ($balances) {
            $balances->balance = (int) $balances->balance;
            return $balances;
        });
        return response()->json([
            'status' => 200,
            'data' => $balances,
            'message' => 'Accounts balances retrieved successfully.',
        ]);
    }

    public function store(AccountsBalance $request)
    {
        $validated = $request->validated();
        $balance = AccountsBalance::create($validated);
        return response()->json([
            'status' => 201,
            'data' => $balance,
            'message' => 'Accounts balance created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $balance = AccountsBalance::with('account')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $balance,
            'message' => 'Accounts balance retrieved successfully.',
        ]);
    }

    public function update(AccountsBalance $request, $id)
    {
        $balance = AccountsBalance::findOrFail($id);
        $validated = $request->validated();
        $balance->update($validated);
        return response()->json([
            'status' => 200,
            'data' => $balance,
            'message' => 'Accounts balance updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        AccountsBalance::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Accounts balance deleted successfully.',
        ]);
    }
}
