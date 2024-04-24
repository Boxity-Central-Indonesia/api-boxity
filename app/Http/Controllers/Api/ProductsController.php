<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;
use App\Models\Warehouse;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Mendapatkan nama kategori dari parameter query string
        $categoryName = $request->query('category_name');

        // Jika tidak ada parameter kategori yang diberikan, ambil semua produk
        if (!$categoryName) {
            $products = Product::with(['warehouse', 'category', 'prices'])
            ->orderBy('name','asc')
                                ->get()
                                ->map(function ($product) {
                                    $product->price = (int) $product->price;
                                    return $product;
                                });

            return response()->json([
                'status' => 200,
                'data' => $products,
                'message' => 'All products retrieved successfully.',
            ]);
        }

        // Jika ada parameter kategori, ambil produk dengan kategori yang sesuai
        $products = Product::with(['warehouse', 'category', 'prices'])
                            ->whereHas('category', function($query) use ($categoryName) {
                                $query->where('name', 'LIKE', "%$categoryName%");
                            })
                            ->orderBy('name','asc')
                            ->get()
                            ->map(function ($product) {
                                $product->price = (int) $product->price;
                                return $product;
                            });

        if ($products->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No products found for the specified category.',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $products,
            'message' => 'Products retrieved successfully.',
        ]);
    }



    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image_product')) {
            $result = cloudinary()->upload($request->file('image_product')->getRealPath())->getSecurePath();
            $data['image_product'] = $result;
        } else {
            $data['image_product'] = 'https://res.cloudinary.com/boxity-id/image/upload/v1709745192/39b09e1f-0446-4f78-bbf1-6d52d4e7e4df.png';
        }
        // Assume 'warehouse_id' is passed from the request, adjust accordingly if it's different
    $warehouseId = $request->input('warehouse_id');

    // Check if the selected warehouse has exceeded its capacity
    $warehouse = Warehouse::findOrFail($warehouseId);
    if ($warehouse->exceedsCapacity($data['stock'])) {
        return response()->json([
            'status' => 400,
            'message' => 'Selected warehouse has exceeded its capacity.',
        ], 400);
    }
        $product = Product::create($data);
        broadcast(new formCreated('New Product created successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $product,
            'message' => 'Product created successfully.',
        ], 201);
    }

    public function update(ProductRequest $request, $id)
{
    $product = Product::findOrFail($id);
    $data = $request->validated();

    // Generate a unique product code only if 'code' is not provided in the request
    if (!isset($data['code'])) {
        $uniqueIdentifier = uniqid(); // You can customize this based on your needs
        $productCode = 'PRD' . strtoupper(substr(md5($uniqueIdentifier), 0, 6));
        $data['code'] = $productCode; // Assign the generated code to the data array
    }

    if ($request->hasFile('image_product')) {
        $result = cloudinary()->upload($request->file('image_product')->getRealPath())->getSecurePath();
        $data['image_product'] = $result;
    } else {
        $data['image_product'] = $product->image_product;
    }

    $product->update($data);
    broadcast(new formCreated('Product updated successfully.'));

    return response()->json([
        'status' => 201,
        'data' => $product,
        'message' => 'Product updated successfully.',
    ]);
}


    public function show($id)
    {
        $product = Product::with(['warehouse', 'category'])->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $product,
            'message' => 'Product retrieved successfully.',
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully.',
        ]);
    }
    public function processingActivities($productId)
    {
        $product = Product::with('processingActivities')->find($productId);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found.',
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $product->processingActivities,
            'message' => 'Processing activities for product retrieved successfully.',
        ]);
    }
}