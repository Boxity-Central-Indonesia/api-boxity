<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\WarehouseLocationRequest;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

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
        return response()->json([
            'status' => 200,
            'data' => $locations
        ]);
    }

    public function store(WarehouseLocationRequest $request)
    {

        $location = WarehouseLocation::create($request->all());
        broadcast(new formCreated('New warehouse location created successfully.'));
        
        return response()->json([
            'status' => 201,
            'data' => $location
        ], 201);
    }

    public function show($id)
    {
        $location = WarehouseLocation::with('warehouse')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $location
        ]);
    }

    public function update(WarehouseLocationRequest $request, $id)
    {

        $location = WarehouseLocation::findOrFail($id);
        $location->update($request->all());
        broadcast(new formCreated('Warehouse location updated successfully.'));
        
        return response()->json([
            'status' => 201,
            'data' => $location
        ], 201);
    }

    public function destroy($id)
    {
        $location = WarehouseLocation::findOrFail($id);
        $location->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
