<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessingActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'sometimes|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'activity_type' => 'sometimes|string',
            'status_activities' => 'sometimes|string',
            'details' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The product ID is required.',
            'product_id.exists' => 'The selected product ID does not exist.',
            'order_id.required' => 'The order ID is required.',
            'order_id.exists' => 'The selected order ID does not exist.',
            'activity_type.required' => 'The activity type is required.',
            'activity_type.string' => 'The activity type must be a string.',
        ];
    }
}
