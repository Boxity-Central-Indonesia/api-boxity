<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies_branches,email,' . $this->branch,
            'company_id' => 'required|integer|exists:companies,id',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Adjust rules for update operation
            $rules = [
                'name' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string|max:255',
                'phone_number' => 'sometimes|required|string|max:255',
                'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('companies_branches')->ignore($this->branch)],
                'company_id' => 'sometimes|required|integer|exists:companies,id',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'email.unique' => 'This email is already in use. Please use a different email.',
            'company_id.required' => 'The company ID field is required.',
            'company_id.integer' => 'The company ID must be an integer.',
            'company_id.exists' => 'The specified company ID does not exist.',
            'name.sometimes' => 'The name field is required when updating.',
            'address.sometimes' => 'The address field is required when updating.',
            'phone_number.sometimes' => 'The phone number field is required when updating.',
            'email.sometimes' => 'The email field is required when updating.',
            'company_id.sometimes' => 'The company ID field is required when updating.',
        ];
    }
}
