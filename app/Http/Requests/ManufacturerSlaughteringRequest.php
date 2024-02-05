<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManufacturerSlaughteringRequest extends FormRequest
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
            'slaughter_date' => 'required|date',
            'method' => 'required|in:halal,electrical stunning,kosher,captive bolt,gas killing,sticking',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'A product ID is required.',
            'product_id.exists' => 'The provided product ID does not exist.',
            'slaughter_date.required' => 'The slaughter date is required.',
            'slaughter_date.date' => 'The slaughter date must be a valid date.',
            'method.required' => 'The slaughtering method is required.',
            'method.in' => 'The selected slaughtering method is invalid. Valid methods include halal, electrical stunning, kosher, captive bolt, gas killing, and sticking.',
        ];
    }
}
