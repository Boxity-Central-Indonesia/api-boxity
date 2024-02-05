<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function rules()
    {
        $rulesForStore = [
            'order_id' => 'required|exists:orders,id',
            'total_amount' => 'required|numeric',
            'balance_due' => 'required|numeric',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,partial,paid',
        ];

        $rulesForUpdate = [
            'total_amount' => 'sometimes|required|numeric',
            'balance_due' => 'sometimes|required|numeric',
            'invoice_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:unpaid,partial,paid',
        ];

        return $this->isMethod('post') ? $rulesForStore : $rulesForUpdate;
    }

    public function messages()
    {
        return [
            'order_id.required' => 'The order ID field is mandatory.',
            'order_id.exists' => 'The provided order ID does not exist.',
            'total_amount.required' => 'The total amount is required.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'balance_due.required' => 'The balance due is required.',
            'balance_due.numeric' => 'The balance due must be a number.',
            'invoice_date.required' => 'The invoice date is required.',
            'invoice_date.date' => 'The invoice date must be a valid date.',
            'due_date.required' => 'The due date is required.',
            'due_date.date' => 'The due date must be a valid date.',
            'status.required' => 'The invoice status is required.',
            'status.in' => 'The provided status is invalid; it must be either unpaid, partial, or paid.',
            // Additional messages for the update operation
            'total_amount.sometimes' => 'The total amount is required for updates when provided.',
            'balance_due.sometimes' => 'The balance due is required for updates when provided.',
            'invoice_date.sometimes' => 'The invoice date is required for updates when provided.',
            'due_date.sometimes' => 'The due date is required for updates when provided.',
            'status.sometimes' => 'The invoice status is required for updates when provided.',
        ];
    }
}
