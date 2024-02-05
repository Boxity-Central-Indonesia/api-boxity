<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CompanyDepartmentRequest;
use App\Models\CompaniesDepartment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Company;

class CompaniesDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = CompaniesDepartment::with('company')->get();

        return response()->json([
            'status' => 200,
            'data' => $departments,
            'message' => 'Departments retrieved successfully.',
        ]);
    }

    public function store(CompanyDepartmentRequest $request)
    {
        $department = CompaniesDepartment::create($request->validated());
        return response()->json([
            'status' => 201,
            'data' => $department,
            'message' => 'Department created successfully.',
        ], 201);
    }

    public function update(CompanyDepartmentRequest $request, $department)
    {
        $department = CompaniesDepartment::findOrFail($department);
        $department->update($request->validated());
        return response()->json([
            'status' => 200,
            'data' => $department,
            'message' => 'Department updated successfully.',
        ]);
    }

    // CompaniesDepartmentController.php

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @param  \App\Models\CompaniesDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company, CompaniesDepartment $department)
    {
        // Assuming 'departments' is the relationship method in the Company model
        $departmentModel = $company->departments()->find($department->id);

        if (!$departmentModel) {
            return response()->json(['message' => 'Department not found.'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $departmentModel,
            'message' => 'Department retrieved successfully.',
        ]);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompaniesDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($company, CompaniesDepartment $department)
    {
        $department->delete();

        return response()->json(null, 204);
    }
}
