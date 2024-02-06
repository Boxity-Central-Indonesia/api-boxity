<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $accounts = Account::all();
        return response()->json(['status' => 200, 'data' => $accounts, 'message' => 'Accounts retrieved successfully.']);
    }
    public function getAccountingData()
    {
        // Melakukan join antara accounts, accounts_transactions, dan accounts_balance
        $accountingData = Account::select(
            'name as account_name',
            'type',
            'balance', // Mengambil balance langsung dari accounts
            'created_at',
            'updated_at',
            'user_created',
            'user_updated'
        )->get();

        return response()->json([
            'status' => 200,
            'data' => [
                'accounting_data' => $accountingData,
            ],
        ]);
    }

    public function store(AccountRequest $request)
    {
        $validated = $request->validated();
        $account = Account::create($validated);
        return response()->json([
            'status' => 201,
            'data' => $account,
            'message' => 'Account created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return response()->json(['status' => 200, 'data' => $account, 'message' => 'Account retrieved successfully.']);
    }

    public function update(AccountRequest $request, $id)
    {
        $account = Account::findOrFail($id);
        $validated = $request->validated();
        $account->update($validated);
        return response()->json([
            'status' => 200,
            'data' => $account,
            'message' => 'Account updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        Account::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Account deleted successfully.']);
    }
}
