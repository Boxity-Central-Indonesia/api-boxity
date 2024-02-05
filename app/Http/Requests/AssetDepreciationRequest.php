<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetDepreciationRequest extends FormRequest
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
            'asset_id' => 'required|exists:assets,id',
            'method' => 'required|in:linear,declining_balance,sum_of_the_years_digits,units_of_production,double_declining_balance',
            'useful_life' => 'required|integer',
            'residual_value' => 'required|numeric',
            'start_date' => 'required|date',
            'current_value' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'asset_id.required' => 'An asset ID is required.',
            'asset_id.exists' => 'The selected asset does not exist.',
            'method.required' => 'A depreciation method is required.',
            'method.in' => 'The selected method is invalid. Valid methods are linear, declining balance, sum of the years digits, units of production, double declining balance.',
            'useful_life.required' => 'The useful life of the asset is required.',
            'useful_life.integer' => 'The useful life must be an integer.',
            'residual_value.required' => 'The residual value of the asset is required.',
            'residual_value.numeric' => 'The residual value must be a number.',
            'start_date.required' => 'The start date of depreciation is required.',
            'start_date.date' => 'The start date must be a valid date.',
            'current_value.required' => 'The current value of the asset is required.',
            'current_value.numeric' => 'The current value must be a number.',
        ];
    }
}
