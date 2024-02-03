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
        $accounts = Account::with(['transactions', 'balances'])->get();

        return response()->json([
            'data' => $accounts,
            'message' => 'Accounts retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'balance' => 'required|numeric|min:0',
        ];

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be one of: asset, liability, equity, income, expense.',
            'balance.required' => 'The balance field is required.',
            'balance.numeric' => 'The balance must be a number.',
            'balance.min' => 'The balance must be at least 0.',
        ];
        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account = Account::create($request->all());

        return response()->json([
            'data' => $account,
            'message' => 'Account created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Account $account
     * @return JsonResponse
     */
    public function show(Account $account)
    {
        return response()->json([
            'data' => $account->load(['transactions', 'balances']),
            'message' => 'Account retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Account $account
     * @return JsonResponse
     */
    public function update(Request $request, Account $account)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'balance' => 'required|numeric|min:0',
        ];

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be one of: asset, liability, equity, income, expense.',
            'balance.required' => 'The balance field is required.',
            'balance.numeric' => 'The balance must be a number.',
            'balance.min' => 'The balance must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $account->update($request->all());

        return response()->json([
            'data' => $account,
            'message' => 'Account updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Account $account
     * @return JsonResponse
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return response()->json(null, 204);
    }
}