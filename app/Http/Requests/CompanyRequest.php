<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        $company = $this->company;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('companies')->ignore($company)],
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            // Add rules for other fields as needed
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Adjust rules for update operation
            $rules['email'] = ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('companies')->ignore($company)];
            // Optionally adjust other fields for 'sometimes' requirement
        }

        return $rules;
    }

    public function messages()
    {
        return [
            // Custom messages for validation rules
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered. Please use a different email.',
            'phone_number.required' => 'The phone number field is required.',
            'address.required' => 'The address field is required.',
            'city.required' => 'The city field is required.',
            'province.required' => 'The province field is required.',
            'postal_code.required' => 'The postal code field is required.',
            'country.required' => 'The country field is required.',
            'industry.required' => 'The industry field is required.',
            // Add custom messages for other fields as needed
        ];
    }
}
