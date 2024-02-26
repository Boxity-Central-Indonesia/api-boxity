<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class WarehousesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warehouses = Warehouse::get()->map(function ($warehouses) {
            $warehouses->capacity = (int) $warehouses->capacity;
            return $warehouses;
        });

        return response()->json([
            'status' => 200,
            'data' => $warehouses,
            'message' => 'Warehouses retrieved successfully.',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:10000',
        ];

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'capacity.required' => 'The capacity field is required.',
            'capacity.numeric' => 'The capacity must be a number.',
            'capacity.min' => 'The capacity must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $warehouse = Warehouse::create($request->all());

        return response()->json([
            'status' => 201,
            'data' => $warehouse,
            'message' => 'Warehouse created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        return response()->json([
            'status' => 200,
            'data' => $warehouse,
            'message' => 'Warehouse retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'capacity' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:10000',
        ];

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'capacity.required' => 'The capacity field is required.',
            'capacity.numeric' => 'The capacity must be a number.',
            'capacity.min' => 'The capacity must be at least 0.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $warehouse->update($request->all());

        return response()->json([
            'status' => 201,
            'data' => $warehouse,
            'message' => 'Warehouse updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return response()->json(null, 204);
    }
}
