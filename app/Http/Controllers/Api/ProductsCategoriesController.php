<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductsCategory;

class ProductsCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $categories = ProductsCategory::all();
        return response()->json([
            'status' => 200,
            'data' => $categories,
            'message' => 'Categories retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ],[
            'name.required' => 'The name field is required.',
            'description.required' => 'The description field is required.',
        ]);

        $category = ProductsCategory::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $category,
            'message' => 'Category created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $category = ProductsCategory::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $category,
            'message' => 'Category retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $category = ProductsCategory::findOrFail($id);
        $category->update($request->all());
        return response()->json([
            'status' => 201,
            'data' => $category,
            'message' => 'Category updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $category = ProductsCategory::findOrFail($id);
        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
