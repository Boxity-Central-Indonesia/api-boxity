<?php

namespace App\Http\Controllers\Api;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $vendors = Vendor::with('contacts')->get();

        return response()->json([
            'data' => $vendors,
            'message' => 'Vendors retrieved successfully.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|string|email',
            'date_of_birth' => 'nullable|date',
            'transaction_type' => 'required|in:outbound,inbound',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $vendor = Vendor::create($request->all());

        return response()->json([
            'data' => $vendor,
            'message' => 'Vendor created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Vendor $vendor
     * @return JsonResponse
     */
    public function show(Vendor $vendor)
    {
        return response()->json([
            'data' => $vendor,
            'message' => 'Vendor retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return JsonResponse
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validationRules = [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'phone_number' => 'sometimes|string',
            'email' => 'sometimes|string|email',
            'date_of_birth' => 'sometimes|date',
            'transaction_type' => 'sometimes|in:outbound,inbound',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $vendor->update($request->all());

        return response()->json([
            'data' => $vendor,
            'message' => 'Vendor updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @return JsonResponse
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return response()->json(null, 204);
    }
}
