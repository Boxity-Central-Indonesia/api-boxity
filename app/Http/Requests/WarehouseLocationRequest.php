<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('id');

        return [
            'warehouse_id' => 'required|exists:warehouses,id',
            'number' => 'required|string',
            'capacity' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'warehouse_id.required' => 'The warehouse ID field is required.',
            'warehouse_id.exists' => 'The selected warehouse ID does not exist in the database.',
            'number.required' => 'The number field is required.',
            'number.string' => 'The number must be a string.',
            'capacity.required' => 'The capacity field is required.',
            'capacity.numeric' => 'The capacity must be a number.',
        ];
    }
}
