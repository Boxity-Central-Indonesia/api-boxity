<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:255', Rule::unique('assets')->ignore($this->asset)],
            'type' => 'required|in:tangible,intangible',
            'description' => 'nullable|string',
            'acquisition_date' => 'required|date',
            'acquisition_cost' => 'required|numeric',
            'book_value' => 'required|numeric',
            'location_id' => 'nullable|exists:asset_locations,id',
            'condition_id' => 'nullable|exists:asset_conditions,id',
        ];

        if ($this->isMethod('post')) {
            // For store operation, enforce unique check without ignoring any asset
            $rules['code'] = 'required|string|max:255|unique:assets';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The asset name is required.',
            'code.required' => 'The asset code is required.',
            'code.unique' => 'The asset code must be unique.',
            'type.required' => 'The asset type is required.',
            'acquisition_date.required' => 'The acquisition date is required.',
            'acquisition_cost.required' => 'The acquisition cost is required and must be numeric.',
            'book_value.required' => 'The book value is required and must be numeric.',
            'location_id.exists' => 'The selected location must exist in the database.',
            'condition_id.exists' => 'The selected condition must exist in the database.',
            // Add more custom messages as needed
        ];
    }
}
