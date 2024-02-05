<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountsBalanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'balance' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'The date field is required.',
            'date.date' => 'The date is not a valid date format.',
            'balance.required' => 'The balance field is required.',
            'balance.numeric' => 'The balance must be a number.',
            'account_id.required' => 'The account ID field is required.',
            'account_id.exists' => 'The selected account ID does not exist.',
        ];
    }
}
