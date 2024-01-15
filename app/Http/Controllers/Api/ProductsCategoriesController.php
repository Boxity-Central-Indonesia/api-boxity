<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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
            'data' => $categories,
            'message' => 'Categories retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = ProductsCategory::create($request->all());

        return response()->json([
            'data' => $category,
            'message' => 'Category created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param ProductsCategory $category
     * @return JsonResponse
     */
    public function show(ProductsCategory $category)
    {
        return response()->json([
            'data' => $category,
            'message' => 'Category retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProductsCategory $category
     * @return JsonResponse
     */
    public function update(Request $request, ProductsCategory $category)
    {
        $validationRules = [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update($request->all());

        return response()->json([
            'data' => $category,
            'message' => 'Category updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductsCategory $category
     * @return JsonResponse
     */
    public function destroy(ProductsCategory $category)
    {
        $category->delete();

        return response()->json(null, 204);
    }
}
