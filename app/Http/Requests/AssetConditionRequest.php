<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetConditionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'condition' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'condition.required' => 'The asset condition field is required and cannot be empty.',
            'condition.string' => 'The asset condition must be a string.',
            'condition.max' => 'The asset condition may not be greater than 255 characters.',
        ];
    }
}
