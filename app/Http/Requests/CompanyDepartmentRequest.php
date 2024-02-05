<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyDepartmentRequest extends FormRequest
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
        $departmentId = $this->department;

        $rules = [
            'name' => 'required|string|max:255',
            'responsibilities' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Adjust rules for update operation
            $rules['name'] = 'sometimes|required|string|max:255';
            $rules['responsibilities'] = 'sometimes|required|string|max:255';
            $rules['company_id'] = 'sometimes|required|exists:companies,id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'responsibilities.required' => 'The responsibilities field is required.',
            'responsibilities.string' => 'The responsibilities must be a string.',
            'responsibilities.max' => 'The responsibilities must not exceed 255 characters.',
            'company_id.required' => 'The company ID field is required.',
            'company_id.exists' => 'The selected company ID does not exist.',
            // Optionally, adjust or add messages for update-specific rules
        ];
    }
}
