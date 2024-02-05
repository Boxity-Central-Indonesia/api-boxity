<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessingActivityRequest extends FormRequest
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
            'carcass_id' => 'required|exists:manufacturer_carcasses,id',
            'activity_type' => 'required|string',
            'details' => 'nullable|array',
        ];
    }

    public function messages()
    {
        return [
            'carcass_id.required' => 'The carcass ID is required.',
            'carcass_id.exists' => 'The selected carcass ID does not exist.',
            'activity_type.required' => 'The activity type is required.',
            'activity_type.string' => 'The activity type must be a string.',
        ];
    }
}
