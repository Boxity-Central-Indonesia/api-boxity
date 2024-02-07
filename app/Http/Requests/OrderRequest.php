<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'vendor_id' => 'required|exists:vendors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'status' => 'required|in:pending,completed,cancelled',
            'details' => 'nullable|string',
            'order_type' => 'required|string',
            'taxes' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price_per_unit' => 'required|numeric',

        ];

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            // For updates, make most fields optional
            foreach ($rules as $key => $rule) {
                if (!in_array($key, ['quantity'])) { // Keep quantity always required
                    $rules[$key] = 'sometimes|' . $rule;
                }
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'vendor_id.required' => 'A vendor ID is required.',
            'vendor_id.exists' => 'The selected vendor ID does not exist.',
            'warehouse_id.required' => 'A warehouse ID is required.',
            'warehouse_id.exists' => 'The selected warehouse ID does not exist.',
            'status.required' => 'Order status is required.',
            'status.in' => 'Invalid order status. Valid statuses are: pending, completed, cancelled.',
            'order_type.required' => 'Order type is required.',
            'order_type.in' => 'Invalid order type. Valid types are: Direct Order, and Production',
            'total_price.required' => 'Total price is required.',
        ];
    }
}
