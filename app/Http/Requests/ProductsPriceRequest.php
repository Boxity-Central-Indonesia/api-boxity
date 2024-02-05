<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductsPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'product_id' => 'required|exists:products,id',
            'selling_price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'discount_price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The product ID field is required.',
            'product_id.exists' => 'The selected product ID does not exist in the database.',
            'selling_price.required' => 'The selling price field is required.',
            'selling_price.numeric' => 'The selling price must be a number.',
            'selling_price.min' => 'The selling price must be at least 0.',
            'buying_price.required' => 'The buying price field is required.',
            'buying_price.numeric' => 'The buying price must be a number.',
            'buying_price.min' => 'The buying price must be at least 0.',
            'discount_price.required' => 'The discount price field is required.',
            'discount_price.numeric' => 'The discount price must be a number.',
            'discount_price.min' => 'The discount price must be at least 0.',
        ];
    }
}
