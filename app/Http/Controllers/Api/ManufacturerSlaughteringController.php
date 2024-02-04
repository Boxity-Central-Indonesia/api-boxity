<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ManufacturerSlaughtering;

class ManufacturerSlaughteringController extends Controller
{
    public function index()
    {
        $slaughtering = ManufacturerSlaughtering::with('product')->get();
        return response()->json([
            'status' => 200,
            'data' => $slaughtering,
            'message' => 'Slaughtering records retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $slaughtering = ManufacturerSlaughtering::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $slaughtering,
            'message' => 'Slaughtering record created successfully.',
        ]);
    }

    public function show($id)
    {
        $slaughtering = ManufacturerSlaughtering::with('product')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $slaughtering,
            'message' => 'Slaughtering record retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $slaughtering = ManufacturerSlaughtering::findOrFail($id);
        $slaughtering->update($request->all());
        return response()->json([
            'status' => 200,
            'data' => $slaughtering,
            'message' => 'Slaughtering record updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        ManufacturerSlaughtering::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Slaughtering record deleted successfully.',
        ]);
    }
}
