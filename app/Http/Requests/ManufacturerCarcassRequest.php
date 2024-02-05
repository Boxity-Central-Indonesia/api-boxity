<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManufacturerCarcassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'slaughtering_id' => 'required|exists:manufacturer_slaughtering,id',
            'weight_after_slaughter' => 'required|numeric|min:0|max:999999.99',
            'quality_grade' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'slaughtering_id.required' => 'The slaughtering ID is required.',
            'slaughtering_id.exists' => 'The provided slaughtering ID does not exist.',
            'weight_after_slaughter.required' => 'The weight after slaughter is required.',
            'weight_after_slaughter.numeric' => 'The weight after slaughter must be a numeric value.',
            'weight_after_slaughter.min' => 'The weight after slaughter cannot be less than 0.',
            'weight_after_slaughter.max' => 'The weight after slaughter cannot be more than 999999.99.',
            'quality_grade.required' => 'The quality grade is required.',
            'quality_grade.string' => 'The quality grade must be a string.',
            'quality_grade.max' => 'The quality grade must not exceed 255 characters.',
        ];
    }
}
