<?php

namespace App\Http\Controllers\Api;

use App\Models\PackageProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackageProductController extends Controller
{
    public function index()
    {
        $packageProducts = PackageProduct::with('product', 'package')->get();

        return response()->json([
            'status' => 200,
            'data' => $packageProducts,
            'message' => 'Package products retrieved successfully.'
        ]);
    }

    // Menampilkan detail PackageProduct berdasarkan ID
    public function show($id)
    {
        $packageProduct = PackageProduct::with('product', 'package')->find($id);

        if (!$packageProduct) {
            return response()->json([
                'status' => 404,
                'message' => 'Package product not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $packageProduct,
            'message' => 'Package product retrieved successfully.'
        ]);
    }

    // Membuat PackageProduct baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id' => 'required|exists:packages,id',
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 400);
        }

        $packageProduct = PackageProduct::create([
            'package_id' => $request->input('package_id'),
            'product_id' => $request->input('product_id'),
        ]);

        return response()->json([
            'status' => 201,
            'data' => $packageProduct,
            'message' => 'Package product created successfully.'
        ], 201);
    }

    // Mengupdate PackageProduct berdasarkan ID
    public function update(Request $request, $id)
    {
        $packageProduct = PackageProduct::find($id);

        if (!$packageProduct) {
            return response()->json([
                'status' => 404,
                'message' => 'Package product not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'package_id' => [
                'required',
                'exists:packages,id',
                Rule::unique('package_products')->ignore($packageProduct->id),
            ],
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 400);
        }

        $packageProduct->update([
            'package_id' => $request->input('package_id'),
            'product_id' => $request->input('product_id'),
        ]);

        return response()->json([
            'status' => 200,
            'data' => $packageProduct,
            'message' => 'Package product updated successfully.'
        ]);
    }

    // Menghapus PackageProduct berdasarkan ID
    public function destroy($id)
    {
        $packageProduct = PackageProduct::find($id);

        if (!$packageProduct) {
            return response()->json([
                'status' => 404,
                'message' => 'Package product not found.'
            ], 404);
        }

        $packageProduct->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Package product deleted successfully.'
        ]);
    }
}
