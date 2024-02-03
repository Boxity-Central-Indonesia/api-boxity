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
