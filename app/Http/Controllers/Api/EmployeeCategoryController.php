<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EmployeeCategoryRequest;
use App\Models\EmployeeCategory;
use Illuminate\Http\Request;
use App\Events\formCreated;
class EmployeeCategoryController extends Controller
{
    public function index()
    {
        $categories = EmployeeCategory::all();
        return response()->json([
            'status' => 200,
            'data' => $categories,
            'message' => 'Employee categories retrieved successfully.',
        ]);
    }

    public function store(EmployeeCategoryRequest $request)
    {
        $category = EmployeeCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return response()->json([
            'status' => 201,
            'data' => $category,
            'message' => 'Employee category created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $category = EmployeeCategory::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $category,
            'message' => 'Employee category retrieved successfully.',
        ]);
    }

    public function update(EmployeeCategoryRequest $request, $id)
    {

        $category = EmployeeCategory::findOrFail($id);
        $category->update($request->all());
        return response()->json([
            'status' => 201,
            'data' => $category,
            'message' => 'Employee category updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $category = EmployeeCategory::findOrFail($id);
        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Employee category deleted successfully.',
        ]);
    }
}
