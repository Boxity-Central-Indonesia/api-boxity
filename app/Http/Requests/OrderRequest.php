<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'status' => [
                'required',
                Rule::in(['pending', 'completed', 'cancelled']),
            ],
            'order_status' => [
                'required',
                Rule::in([
                    'Pending Confirmation',
                    'In Production',
                    'Packaging',
                    'Completed',
                    'Cancelled',
                    'Shipped'
                ]),
            ],
            'details' => 'nullable|string',
            'no_ref' => 'nullable|string',
            'order_type' => [
                'required',
                Rule::in(['Direct Order', 'Production']),
            ],
            'taxes' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
            'products' => 'required|array',
            'products.*' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price_per_unit' => 'required|numeric',
        ];

        // Handling for PUT/PATCH requests (update)
        if ($this->isMethod('patch') || $this->isMethod('put')) {
            // Only `quantity` should be required for updates
            foreach ($rules as $key => $rule) {
                if ($key === 'products.*.quantity') {
                    continue;
                }

                $rules[$key] = 'sometimes|' . $rule;
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'vendor_id.required' => 'A vendor ID is required.',
            'vendor_id.exists' => 'The selected vendor ID does not exist.',
            'warehouse_id.nullable' => 'A warehouse ID is optional.',
            'warehouse_id.exists' => 'The selected warehouse ID does not exist.',
            'status.required' => 'Order status is required.',
            'status.in' => 'Invalid order status. Valid statuses are: pending, completed, cancelled.',
            'order_type.required' => 'Order type is required.',
            'order_type.in' => 'Invalid order type. Valid types are: Direct Order, Production',
            'total_price.required' => 'Total price is required.',
            'products.*.product_id.required' => 'A product ID is required for each product.',
            'products.*.product_id.exists' => 'The selected product ID does not exist.',
            'products.*.quantity.required' => 'Quantity is required for each product.',
            'products.*.quantity.min' => 'Quantity must be at least 1 for each product.',
            'products.*.price_per_unit.required' => 'Price per unit is required for each product.',
            'products.*.price_per_unit.numeric' => 'Price per unit must be a number for each product.',
        ];
    }
}
