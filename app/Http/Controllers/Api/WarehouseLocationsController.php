<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WarehouseLocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = WarehouseLocation::with('warehouse')->get();
        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'number' => 'required|string',
            'capacity' => 'required|numeric',
        ]);

        $location = WarehouseLocation::create($request->all());
        return response()->json($location, 201);
    }

    public function show($id)
    {
        $location = WarehouseLocation::with('warehouse')->findOrFail($id);
        return response()->json($location);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'number' => 'required|string',
            'capacity' => 'required|numeric',
        ]);

        $location = WarehouseLocation::findOrFail($id);
        $location->update($request->all());
        return response()->json($location);
    }

    public function destroy($id)
    {
        $location = WarehouseLocation::findOrFail($id);
        $location->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
