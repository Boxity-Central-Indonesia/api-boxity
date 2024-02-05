<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'name' => 'required|string',
            'code' => 'required|string|unique:products,code,' . $id,
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:products_categories,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'weight' => 'nullable|numeric|min:0',
            'animal_type' => 'nullable|string',
            'age' => 'nullable|integer|min:0',
            'health_status' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'code.required' => 'The code field is required.',
            'code.unique' => 'The code has already been taken.',
            'description.required' => 'The description field is required.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a numeric value.',
            'category_id.exists' => 'The selected category is invalid.',
            'warehouse_id.exists' => 'The selected warehouse is invalid.',
            'weight.numeric' => 'The weight must be a numeric value.',
            'weight.min' => 'The weight must be at least 0.',
            'age.integer' => 'The age must be an integer.',
            'age.min' => 'The age must be at least 0.',
        ];
    }
}
