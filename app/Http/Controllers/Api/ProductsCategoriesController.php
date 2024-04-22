<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductsCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductsCategory;
use App\Events\formCreated;

class ProductsCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $query = $request->query('name');

    // Jika ada query parameter 'name', maka filter berdasarkan nama kategori
    if($query) {
        $categories = ProductsCategory::where('name', 'LIKE', "%$query%")->orderBy('name','asc')->get();
    } else {
        $categories = ProductsCategory::orderBy('name','asc')->get();
    }

    return response()->json([
        'status' => 200,
        'data' => $categories,
        'message' => 'Categories retrieved successfully.',
    ]);
    }

    public function store(ProductsCategoryRequest $request)
    {
        $category = ProductsCategory::create($request->all());
        broadcast(new formCreated('New Category created successfully.'));

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
        broadcast(new formCreated('Category updated successfully.'));

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
