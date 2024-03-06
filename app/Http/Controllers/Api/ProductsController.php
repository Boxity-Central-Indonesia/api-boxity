<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['warehouse', 'category', 'prices'])->get()->map(function ($product) {
            $product->price = (int) $product->price;
            return $product;
        });
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
