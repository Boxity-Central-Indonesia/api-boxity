<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PackageProductRequest;
use App\Models\PackageProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Events\formCreated;

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
    public function store(PackageProductRequest $request)
    {

        $packageProduct = PackageProduct::create($request->validated());
broadcast(new formCreated('New Package product created successfully.'));
        
        return response()->json([
            'status' => 201,
            'data' => $packageProduct,
            'message' => 'Package product created successfully.'
        ], 201);
    }

    // Mengupdate PackageProduct berdasarkan ID
    public function update(PackageProductRequest $request, $id)
    {
        $packageProduct = PackageProduct::find($id);

        if (!$packageProduct) {
            return response()->json([
                'status' => 404,
                'message' => 'Package product not found.'
            ], 404);
        }

        $packageProduct->update($request->validated());

        broadcast(new formCreated('Package product updated successfully.'));
        
        return response()->json([
            'status' => 201,
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
