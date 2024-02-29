<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

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
            'status' => 200,
            'data' => $employees,
            'message' => 'Employees retrieved successfully.',
        ]);
    }
    public function store(EmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());
broadcast(new formCreated('New Employee created successfully.'));
        return response()->json([
            'status' => 201,
            'data' => $employee,
            'message' => 'Employee created successfully.',
        ], 201);
    }

    public function update(EmployeeRequest $request, $employee)
    {
        $employee = Employee::findOrFail($employee);
        $employee->update($request->validated());
broadcast(new formCreated('New Employee created successfully.'));
        return response()->json([
            'status' => 201,
            'data' => $employee,
            'message' => 'Employee updated successfully.',
        ]);
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
        $employeeWithCompany = Employee::with('company', 'category')->find($employee->id);

        return response()->json([
            'status' => 200,
            'data' => $employeeWithCompany,
            'message' => 'Employee retrieved successfully.',
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
