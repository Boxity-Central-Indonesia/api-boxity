<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManufacturerVisceraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'carcass_id' => 'required|exists:manufacturer_carcasses,id',
            'type' => 'required|string|max:255',
            'handling_method' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'carcass_id.required' => 'The carcass ID is required.',
            'carcass_id.exists' => 'The selected carcass ID does not exist.',
            'type.required' => 'The type of viscera is required.',
            'type.string' => 'The type must be a string.',
            'type.max' => 'The type must not exceed 255 characters.',
            'handling_method.required' => 'The handling method is required.',
            'handling_method.string' => 'The handling method must be a string.',
            'handling_method.max' => 'The handling method must not exceed 255 characters.',
        ];
    }
}
