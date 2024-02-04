<?php

namespace App\Http\Controllers\Api;

use App\Models\Packaging;
use Illuminate\Http\Request;

class PackagingController extends Controller
{
    public function index()
    {
        $packagings = Packaging::with('product')->get();
        return response()->json([
            'status' => 200,
            'data' => $packagings,
            'message' => 'Packaging records retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $packaging = Packaging::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $packaging,
            'message' => 'Packaging record created successfully.',
        ]);
    }

    public function show($id)
    {
        $packaging = Packaging::with('product')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $packaging,
            'message' => 'Packaging record retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $packaging = Packaging::findOrFail($id);
        $packaging->update($request->all());
        return response()->json([
            'status' => 200,
            'data' => $packaging,
            'message' => 'Packaging record updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        Packaging::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Packaging record deleted successfully.',
        ]);
    }
}
