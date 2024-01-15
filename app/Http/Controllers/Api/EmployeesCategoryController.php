<?php

namespace App\Http\Controllers\Api;

use App\Models\EmployeesCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class EmployeesCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = EmployeesCategory::all();

        return response()->json([
            'data' => $categories,
            'message' => 'Categories retrieved successfully.',
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
            'description' => 'nullable|string|max:100000',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = EmployeesCategory::create($request->all());

        return response()->json([
            'data' => $category,
            'message' => 'Employee Category created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmployeesCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeesCategory $category)
    {
        return response()->json([
            'data' => $category,
            'message' => 'Employee Category retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmployeesCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeesCategory $category)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:100000',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update($request->all());

        return response()->json([
            'data' => $category,
            'message' => 'Employee Category updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeesCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeesCategory $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }
}
