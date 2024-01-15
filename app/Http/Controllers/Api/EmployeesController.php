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
        $employees = Employee::all();

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
            'job_title_category_id' => 'required|integer|exists:employees_categories,id',
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

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employee = Employee::create($request->all());

        return response()->json([
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
        return response()->json([
            'data' => $employee,
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
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:employees,email,' . $employee->id,
            'phone_number' => 'string|max:255',
            'company_id' => 'integer|exists:companies,id',
            'job_title_category_id' => 'integer|exists:employees_categories,id',
            'job_title' => 'string|max:255',
            'date_of_birth' => 'date',
            'employment_status' => 'string',
            'hire_date' => 'date',
            'address' => 'string|max:255',
            'city' => 'string|max:255',
            'province' => 'string|max:255',
            'postal_code' => 'string|max:255',
            'country' => 'string|max:255',
            'emergency_contact_name' => 'string|max:255',
            'emergency_contact_phone_number' => 'string|max:255',
            'department_id' => 'integer|exists:companies_departments,id',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employee->update($request->all());

        return response()->json([
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
