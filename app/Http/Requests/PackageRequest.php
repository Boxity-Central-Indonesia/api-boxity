<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRequest extends FormRequest
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
        $packageId = $this->package;

        return [
            'package_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('packages', 'package_name')->ignore($packageId),
            ],
            'package_weight' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'package_name.required' => 'The package name is required.',
            'package_name.string' => 'The package name must be a string.',
            'package_name.max' => 'The package name must not exceed 255 characters.',
            'package_name.unique' => 'The package name has already been taken.',
            'package_weight.required' => 'The package weight is required.',
            'package_weight.numeric' => 'The package weight must be a numeric value.',
        ];
    }
}
