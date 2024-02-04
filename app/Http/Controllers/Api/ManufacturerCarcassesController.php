<?php

namespace App\Http\Controllers\Api;

use App\Models\ManufacturerCarcass;
use Illuminate\Http\Request;

class ManufacturerCarcassesController extends Controller
{
    public function index()
    {
        $carcasses = ManufacturerCarcass::with('slaughtering.product')->get();
        return response()->json([
            'status' => 200,
            'data' => $carcasses,
            'message' => 'Carcasses records retrieved successfully.',
        ]);
    }

    public function store(Request $request)
    {
        $carcass = ManufacturerCarcass::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $carcass,
            'message' => 'Carcass record created successfully.',
        ]);
    }

    public function show($id)
    {
        $carcass = ManufacturerCarcass::with('slaughtering.product')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $carcass,
            'message' => 'Carcass record retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $carcass = ManufacturerCarcass::findOrFail($id);
        $carcass->update($request->all());
        return response()->json([
            'status' => 200,
            'data' => $carcass,
            'message' => 'Carcass record updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        ManufacturerCarcass::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Carcass record deleted successfully.',
        ]);
    }
}
