<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::all();

        return response()->json([
            'status' => 200,
            'data' => $packages,
            'message' => 'Packages retrieved successfully.'
        ]);
    }

    // Menampilkan detail Package berdasarkan ID
    public function show($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'Package not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $package,
            'message' => 'Package retrieved successfully.'
        ]);
    }

    // Membuat Package baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required|unique:packages,package_name',
            'package_weight' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 400);
        }

        $package = Package::create([
            'package_name' => $request->input('package_name'),
            'package_weight' => $request->input('package_weight'),
        ]);

        return response()->json([
            'status' => 201,
            'data' => $package,
            'message' => 'Package created successfully.'
        ], 201);
    }

    // Mengupdate Package berdasarkan ID
    public function update(Request $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'Package not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'package_name' => [
                'required',
                Rule::unique('packages')->ignore($package->id),
            ],
            'package_weight' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 400);
        }

        $package->update([
            'package_name' => $request->input('package_name'),
            'package_weight' => $request->input('package_weight'),
        ]);

        return response()->json([
            'status' => 200,
            'data' => $package,
            'message' => 'Package updated successfully.'
        ]);
    }

    // Menghapus Package berdasarkan ID
    public function destroy($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'Package not found.'
            ], 404);
        }

        $package->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Package deleted successfully.'
        ]);
    }

    // Fungsi untuk mengambil Package beserta daftar produk yang terkait (fungsi with)
    public function withProducts($id)
    {
        $package = Package::with('products')->find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'Package not found.'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $package,
            'message' => 'Package with products retrieved successfully.'
        ]);
    }
}
