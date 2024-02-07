<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'code' => 'required|string|unique:products,code',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:products_categories,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'type' => 'nullable|string',
            'animal_type' => 'nullable|string',
            'age' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'health_status' => 'nullable|string',
            'stock' => 'nullable|numeric|min:0',
            'unit_of_measure' => 'nullable|string',
            'raw_material' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please provide a name for the product.',
            'code.required' => 'A unique product code is required.',
            'code.unique' => 'This product code is already in use. Please use a different code.',
            'description.required' => 'Please provide a description for the product.',
            'price.required' => 'Please specify the price of the product.',
            'price.numeric' => 'The price must be a number. Please enter a valid numeric value.',
            'category_id.exists' => 'The selected category does not exist. Please choose a valid category.',
            'warehouse_id.exists' => 'The selected warehouse does not exist. Please choose a valid warehouse.',
            'weight.numeric' => 'Weight must be a number. Please enter a valid numeric value for weight.',
            'weight.min' => 'Weight cannot be negative. Please enter a weight of 0 or more.',
            'age.integer' => 'Age must be a whole number. Please enter a valid integer value for age.',
            'age.min' => 'Age cannot be negative. Please enter an age of 0 or more.',
            'stock.string' => 'The stock field should be a string value.',
            'unit_of_measure.string' => 'Please specify the unit of measure as a string.',
            'raw_material.string' => 'Please specify if the product is a raw material as a string value ("yes" or "no").',
        ];
    }
}
