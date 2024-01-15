<?php

namespace App\Http\Controllers\Api;

use App\Models\CompaniesDepartment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompaniesDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = CompaniesDepartment::get();

        return response()->json([
            'data' => $departments,
            'message' => 'Departments retrieved successfully.',
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
            'company_id' => 'required|exists:companies,id',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $department = CompaniesDepartment::create($request->all());

        $department->save();

        return response()->json([
            'data' => $department,
            'message' => 'Department created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompaniesDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function show($company, $department)
    {
        $departmentModel = CompaniesDepartment::with('company')->find($department);

        if (!$departmentModel) {
            return response()->json(['message' => 'Department not found.'], 404);
        }

        return response()->json([
            'data' => $departmentModel,
            'message' => 'Department retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompaniesDepartment  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $company, $department)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ];
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            Log::info('Validation errors:', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }

        $branchModel = CompaniesDepartment::find($department);

        if (!$branchModel) {
            return response()->json(['message' => 'Department not found.'], 404);
        }

        $branchModel->update($request->all());

        return response()->json([
            'data' => $branchModel,
            'message' => 'Department updated successfully.',
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
