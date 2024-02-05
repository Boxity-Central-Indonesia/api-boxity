<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
        $employeeId = $this->employee;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('employees')->ignore($employeeId)],
            'phone_number' => 'required|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
            'job_title' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'employment_status' => 'required|string',
            'hire_date' => 'required|date',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone_number' => 'required|string|max:255',
            'department_id' => 'required|integer|exists:companies_departments,id',
        ];
    }

    public function messages()
    {
        return [
            // Custom messages for validation rules
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email has already been taken.',
            'phone_number.required' => 'The phone number field is required.',
            'company_id.required' => 'The company ID field is required.',
            'company_id.exists' => 'The selected company ID does not exist.',
            'job_title.required' => 'The job title field is required.',
            'date_of_birth.required' => 'The date of birth field is required.',
            'employment_status.required' => 'The employment status field is required.',
            'hire_date.required' => 'The hire date field is required.',
            'address.required' => 'The address field is required.',
            'city.required' => 'The city field is required.',
            'province.required' => 'The province field is required.',
            'postal_code.required' => 'The postal code field is required.',
            'country.required' => 'The country field is required.',
            'emergency_contact_name.required' => 'The emergency contact name field is required.',
            'emergency_contact_phone_number.required' => 'The emergency contact phone number field is required.',
            'department_id.required' => 'The department ID field is required.',
            'department_id.exists' => 'The selected department ID does not exist.',
            // Optionally, adjust or add messages for other fields as needed
        ];
    }
}
