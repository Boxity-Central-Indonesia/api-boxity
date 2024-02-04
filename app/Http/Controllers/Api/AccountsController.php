<?php

namespace App\Http\Controllers\Api;

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
            'accounts.id',
            'accounts.name as account_name',
            'accounts_transactions.id as transaction_id',
            'accounts_transactions.date as transaction_date',
            'accounts_transactions.type as transaction_type',
            'accounts_transactions.amount as transaction_amount',
            'accounts_transactions.description as transaction_description',
            'accounts_balances.balance as account_balance'
        )
            ->leftJoin('accounts_transactions', 'accounts_transactions.account_id', '=', 'accounts.id')
            ->leftJoin('accounts_balances', 'accounts_balances.account_id', '=', 'accounts.id')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => [
                'accounting_data' => $accountingData,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Aset,Liabilitas,Ekuitas,Pendapatan,Pengeluaran,Biaya',
            'balance' => 'required|numeric',
        ]);

        $account = Account::create($validated);
        return response()->json(['status' => 201, 'data' => $account, 'message' => 'Account created successfully.'], 201);
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return response()->json(['status' => 200, 'data' => $account, 'message' => 'Account retrieved successfully.']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Aset,Liabilitas,Ekuitas,Pendapatan,Pengeluaran,Biaya',
            'balance' => 'required|numeric',
        ]);

        $account = Account::findOrFail($id);
        $account->update($validated);
        return response()->json(['status' => 200, 'data' => $account, 'message' => 'Account updated successfully.']);
    }

    public function destroy($id)
    {
        Account::destroy($id);
        return response()->json(['status' => 200, 'message' => 'Account deleted successfully.']);
    }
}
