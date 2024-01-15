<?php

namespace App\Http\Controllers\Api;

use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class VendorContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $contacts = VendorContact::with('vendor')->get();

        return response()->json([
            'data' => $contacts,
            'message' => 'Vendor contacts retrieved successfully.',
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
            'vendors_id' => 'required|integer|exists:vendors,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $contact = VendorContact::create($request->all());

        return response()->json([
            'data' => $contact,
            'message' => 'Vendor contact created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param VendorContact $contact
     * @return JsonResponse
     */
    public function show(VendorContact $contact)
    {
        return response()->json([
            'data' => $contact,
            'message' => 'Vendor contact retrieved successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param VendorContact $contact
     * @return JsonResponse
     */
    public function update(Request $request, VendorContact $contact)
    {
        $validationRules = [
            'name' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:255',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $contact->update($request->all());

        return response()->json([
            'data' => $contact,
            'message' => 'Vendor contact updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param VendorContact $contact
     * @return JsonResponse
     */
    public function destroy(VendorContact $contact)
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}
