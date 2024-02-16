<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductsCategoryRequest;
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

    public function store(ProductsCategoryRequest $request)
    {
        $category = ProductsCategory::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $category,
            'message' => 'Category created successfully.',
        ], 201);
    }

    public function update(ProductsCategoryRequest $request, $id)
    {
        $category = ProductsCategory::findOrFail($id);
        $category->update($request->all());
        return response()->json([
            'status' => 201,
            'data' => $category,
            'message' => 'Category updated successfully.',
        ]);
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
