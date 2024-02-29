<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PackageRequest;
use App\Models\Package;
use App\Models\PackageProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Events\formCreated;

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
    public function store(PackageRequest $request)
    {
        $package = Package::create($request->validated());
        $itemCount = PackageProduct::where('package_id', $package->id)->count();
        if ($itemCount > 0) {
            // Membuat produk baru yang mewakili paket jika ada satu atau lebih item
            $product = Product::create([
                'name' => $request->input('package_name'), // Nama paket sebagai nama produk
                'code' => 'PKG-' . strtoupper(uniqid()), // Membuat kode unik
                'description' => 'Package product with ' . $itemCount . ' items',
                'price' => 0, // Tentukan harga sesuai kebutuhan
                'weight' => $request->input('package_weight'),
                // Sesuaikan dengan kolom yang ada di tabel products
            ]);
        }broadcast(new formCreated('New Package created successfully.'));
        return response()->json([
            'status' => 201,
            'data' => $package,
            'message' => 'Package created successfully.'
        ], 201);
    }

    // Mengupdate Package berdasarkan ID
    public function update(PackageRequest $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'Package not found.'
            ], 404);
        }

        $package->update($request->validated());
        $product = Product::where('name', $package->package_name)->first();
        if ($product) {
            $product->update([
                'name' => $request->input('package_name'), // atau gunakan nama lain jika paket diubah
                'weight' => $request->input('package_weight'),
            ]);
        }
        broadcast(new formCreated('Package updated successfully.'));
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
