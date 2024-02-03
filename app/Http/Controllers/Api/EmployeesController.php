<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::with('company')->get();

        return response()->json([
            'data' => $employees,
            'message' => 'Employees retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees',
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

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'email.unique' => 'The email has already been taken.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
            'company_id.required' => 'The company ID field is required.',
            'company_id.integer' => 'The company ID must be an integer.',
            'company_id.exists' => 'The selected company ID does not exist in the database.',
            'job_title.required' => 'The job title field is required.',
            'job_title.string' => 'The job title must be a string.',
            'job_title.max' => 'The job title must not exceed 255 characters.',
            'date_of_birth.required' => 'The date of birth field is required.',
            'date_of_birth.date' => 'Please provide a valid date of birth.',
            'employment_status.required' => 'The employment status field is required.',
            'employment_status.string' => 'The employment status must be a string.',
            'hire_date.required' => 'The hire date field is required.',
            'hire_date.date' => 'Please provide a valid hire date.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'city.required' => 'The city field is required.',
            'city.string' => 'The city must be a string.',
            'city.max' => 'The city must not exceed 255 characters.',
            'province.required' => 'The province field is required.',
            'province.string' => 'The province must be a string.',
            'province.max' => 'The province must not exceed 255 characters.',
            'postal_code.required' => 'The postal code field is required.',
            'postal_code.string' => 'The postal code must be a string.',
            'postal_code.max' => 'The postal code must not exceed 255 characters.',
            'country.required' => 'The country field is required.',
            'country.string' => 'The country must be a string.',
            'country.max' => 'The country must not exceed 255 characters.',
            'emergency_contact_name.required' => 'The emergency contact name field is required.',
            'emergency_contact_name.string' => 'The emergency contact name must be a string.',
            'emergency_contact_name.max' => 'The emergency contact name must not exceed 255 characters.',
            'emergency_contact_phone_number.required' => 'The emergency contact phone number field is required.',
            'emergency_contact_phone_number.string' => 'The emergency contact phone number must be a string.',
            'emergency_contact_phone_number.max' => 'The emergency contact phone number must not exceed 255 characters.',
            'department_id.required' => 'The department ID field is required.',
            'department_id.integer' => 'The department ID must be an integer.',
            'department_id.exists' => 'The selected department ID does not exist in the database.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employee = Employee::create($request->all());

        return response()->json([
            'status' => 201,
            'data' => $employee,
            'message' => 'Employee created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        // Eager load the 'company' relationship
    $employeeWithCompany = Employee::with('company')->find($employee->id);

    return response()->json([
        'status' => 200,
        'data' => $employeeWithCompany,
        'message' => 'Employee retrieved successfully.',
    ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
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

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
            'company_id.required' => 'The company ID field is required.',
            'company_id.integer' => 'The company ID must be an integer.',
            'company_id.exists' => 'The selected company ID does not exist in the database.',
            'job_title.required' => 'The job title field is required.',
            'job_title.string' => 'The job title must be a string.',
            'job_title.max' => 'The job title must not exceed 255 characters.',
            'date_of_birth.required' => 'The date of birth field is required.',
            'date_of_birth.date' => 'Please provide a valid date of birth.',
            'employment_status.required' => 'The employment status field is required.',
            'employment_status.string' => 'The employment status must be a string.',
            'hire_date.required' => 'The hire date field is required.',
            'hire_date.date' => 'Please provide a valid hire date.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'city.required' => 'The city field is required.',
            'city.string' => 'The city must be a string.',
            'city.max' => 'The city must not exceed 255 characters.',
            'province.required' => 'The province field is required.',
            'province.string' => 'The province must be a string.',
            'province.max' => 'The province must not exceed 255 characters.',
            'postal_code.required' => 'The postal code field is required.',
            'postal_code.string' => 'The postal code must be a string.',
            'postal_code.max' => 'The postal code must not exceed 255 characters.',
            'country.required' => 'The country field is required.',
            'country.string' => 'The country must be a string.',
            'country.max' => 'The country must not exceed 255 characters.',
            'emergency_contact_name.required' => 'The emergency contact name field is required.',
            'emergency_contact_name.string' => 'The emergency contact name must be a string.',
            'emergency_contact_name.max' => 'The emergency contact name must not exceed 255 characters.',
            'emergency_contact_phone_number.required' => 'The emergency contact phone number field is required.',
            'emergency_contact_phone_number.string' => 'The emergency contact phone number must be a string.',
            'emergency_contact_phone_number.max' => 'The emergency contact phone number must not exceed 255 characters.',
            'department_id.required' => 'The department ID field is required.',
            'department_id.integer' => 'The department ID must be an integer.',
            'department_id.exists' => 'The selected department ID does not exist in the database.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employee->update($request->all());

        return response()->json([
            'status' => 201,
            'data' => $employee,
            'message' => 'Employee updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */


    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json(null, 204);
    }
}
