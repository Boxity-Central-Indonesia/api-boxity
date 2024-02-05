<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackagingRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'weight' => 'required|numeric',
            'package_type' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'The product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'weight.required' => 'The weight is required.',
            'weight.numeric' => 'The weight must be a number.',
            'package_type.required' => 'The package type is required.',
            'package_type.string' => 'The package type must be a string.',
        ];
    }
}
