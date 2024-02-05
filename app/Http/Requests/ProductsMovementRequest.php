<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductsMovementRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'movement_type' => 'required|in:purchase,sale,transfer',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The product ID field is required.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'warehouse_id.required' => 'The warehouse ID field is required.',
            'warehouse_id.exists' => 'The selected warehouse ID does not exist in the database.',
            'movement_type.required' => 'The movement type field is required.',
            'movement_type.in' => 'The movement type must be one of: purchase, sale, transfer.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be an integer.',
            'quantity.min' => 'The quantity must be at least 1.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
        ];
    }
}
