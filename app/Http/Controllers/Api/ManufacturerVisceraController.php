<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ManufacturerVisceraRequest;
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
    public function store(ManufacturerVisceraRequest $request)
    {
        $viscerum = ManufacturerViscera::create($request->validated());
        return response()->json([
            'status' => 201,
            'data' => $viscerum,
            'message' => 'Viscerum record created successfully.',
        ]);
    }

    public function update(ManufacturerVisceraRequest $request, $id)
    {
        $viscerum = ManufacturerViscera::findOrFail($id);
        $viscerum->update($request->validated());
        return response()->json([
            'status' => 200,
            'data' => $viscerum,
            'message' => 'Viscerum record updated successfully.',
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

    public function destroy($id)
    {
        ManufacturerViscera::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Viscerum record deleted successfully.',
        ]);
    }
}
