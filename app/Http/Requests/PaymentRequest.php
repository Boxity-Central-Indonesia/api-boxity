<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required|in:cash,credit,online,other',
            'payment_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'invoice_id.required' => 'The invoice ID is required.',
            'invoice_id.exists' => 'The selected invoice ID does not exist.',
            'amount_paid.required' => 'The amount paid is required.',
            'amount_paid.numeric' => 'The amount paid must be a numeric value.',
            'payment_method.required' => 'The payment method is required.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'payment_date.required' => 'The payment date is required.',
            'payment_date.date' => 'The payment date must be a valid date.',
        ];
    }
}
