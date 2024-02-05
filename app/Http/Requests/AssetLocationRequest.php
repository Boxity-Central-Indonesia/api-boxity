<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetLocationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required and cannot be left blank.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'address.required' => 'The address field is required and cannot be left blank.',
            'address.string' => 'The address must be a string.',
        ];
    }
}
