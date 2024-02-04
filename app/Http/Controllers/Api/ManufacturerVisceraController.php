<?php

namespace App\Http\Controllers\Api;

use App\Models\ManufacturerViscera;
use Illuminate\Http\Request;

class ManufacturerVisceraController extends Controller
{
    public function index()
    {
        $viscera = ManufacturerViscera::with('carcass.slaughtering.product')->get();
        return response()->json([
            'status' => 200,
            'data' => $viscera,
            'message' => 'Viscera records retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $viscerum = ManufacturerViscera::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $viscerum,
            'message' => 'Viscerum record created successfully.',
        ]);
    }

    public function show($id)
    {
        $viscerum = ManufacturerViscera::with('carcass.slaughtering.product')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $viscerum,
            'message' => 'Viscerum record retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $viscerum = ManufacturerViscera::findOrFail($id);
        $viscerum->update($request->all());
        return response()->json([
            'status' => 200,
            'data' => $viscerum,
            'message' => 'Viscerum record updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        ManufacturerViscera::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Viscerum record deleted successfully.',
        ]);
    }
}
