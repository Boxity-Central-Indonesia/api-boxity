<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountsTransactionRequest extends FormRequest
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
            'type' => 'required|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'account_id' => 'required|exists:accounts,id',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'The type field is required.',
            'date.required' => 'The date field is required.',
            'date.date' => 'The date is not a valid date format.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'account_id.required' => 'The account ID field is required.',
            'account_id.exists' => 'The selected account ID does not exist.',
        ];
    }
}
