<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        $rules = [
            'vendor_id' => 'required|exists:vendors,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'status' => 'required|in:pending,completed,cancelled',
            'details' => 'nullable|string',
            'price_per_unit' => 'required|numeric',
            'total_price' => 'required|numeric',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric',
            'taxes' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
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
            'price_per_unit.required' => 'Price per unit is required.',
            'total_price.required' => 'Total price is required.',
            'product_id.required' => 'A product ID is required.',
            'product_id.exists' => 'The selected product ID does not exist.',
            'quantity.required' => 'Quantity is required.',
            // Add custom messages for other fields as necessary
        ];
    }
}
