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
        $balances = AccountsBalance::with('account')->get();
        return response()->json([
            'status' => 200,
            'data' => $balances,
            'message' => 'Accounts balances retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'balance' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ]);

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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'balance' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ]);

        $balance = AccountsBalance::findOrFail($id);
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
