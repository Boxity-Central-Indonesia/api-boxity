<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PackagingRequest;
use App\Models\Packaging;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(PackagingRequest $request)
    {
        DB::beginTransaction();
        try {
            $packaging = Packaging::create($request->validated());
            // Additional logic here if needed
            DB::commit();
            return response()->json([
                'status' => 201,
                'data' => $packaging,
                'message' => 'Packaging record created successfully.',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to create packaging record. Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(PackagingRequest $request, $id)
    {
        $packaging = Packaging::findOrFail($id);
        DB::beginTransaction();
        try {
            $packaging->update($request->validated());
            // Additional logic here if needed
            DB::commit();
            return response()->json([
                'status' => 200,
                'data' => $packaging,
                'message' => 'Packaging record updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update packaging record. Error: ' . $e->getMessage(),
            ], 500);
        }
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

    public function destroy($id)
    {
        Packaging::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Packaging record deleted successfully.',
        ]);
    }
}
