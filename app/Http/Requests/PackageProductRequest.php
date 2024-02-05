<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $packageProductId = $this->packageProduct;

        return [
            'package_id' => [
                'required',
                'exists:packages,id',
                Rule::unique('package_products')->ignore($packageProductId),
            ],
            'product_id' => 'required|exists:products,id',
        ];
    }

    public function messages()
    {
        return [
            'package_id.required' => 'The package ID is required.',
            'package_id.exists' => 'The selected package does not exist.',
            'package_id.unique' => 'This package has already been associated with another product.',
            'product_id.required' => 'The product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
        ];
    }
}
