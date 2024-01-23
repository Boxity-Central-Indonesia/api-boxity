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
    public function index(Warehouse $warehouse)
    {
        $locations = $warehouse->locations;

        return response()->json([
            'data' => $locations,
            'message' => 'Warehouse locations retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @param  Warehouse $warehouse
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Warehouse $warehouse)
    {
        $validationRules = [
            'number' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
        ];

        $customMessages = [
            'number.required' => 'The number field is required.',
            'number.string' => 'The number must be a string.',
            'number.max' => 'The number must not exceed 255 characters.',
            'capacity.required' => 'The capacity field is required.',
            'capacity.numeric' => 'The capacity must be a number.',
            'capacity.min' => 'The capacity must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $location = $warehouse->locations()->create($request->all());

        return response()->json([
            'data' => $location,
            'message' => 'Warehouse location created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Warehouse $warehouse
     * @param  WarehouseLocation  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse, WarehouseLocation $location)
    {
        return response()->json([
            'data' => $location,
            'message' => 'Warehouse location retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Warehouse $warehouse
     * @param  WarehouseLocation  $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse, WarehouseLocation $location)
    {
        $validationRules = [
            'number' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
        ];

        $customMessages = [
            'number.required' => 'The number field is required.',
            'number.string' => 'The number must be a string.',
            'number.max' => 'The number must not exceed 255 characters.',
            'capacity.required' => 'The capacity field is required.',
            'capacity.numeric' => 'The capacity must be a number.',
            'capacity.min' => 'The capacity must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $location->update($request->all());

        return response()->json([
            'data' => $location,
            'message' => 'Warehouse location updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Warehouse $warehouse
     * @param  WarehouseLocation  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse, WarehouseLocation $location)
    {
        $location->delete();

        return response()->json(null, 204);
    }
}
