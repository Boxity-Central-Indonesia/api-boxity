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

        $customMessages = [
            'vendors_id.required' => 'The vendors ID field is required.',
            'vendors_id.integer' => 'The vendors ID must be an integer.',
            'vendors_id.exists' => 'The selected vendors ID does not exist in the database.',
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'position.required' => 'The position field is required.',
            'position.string' => 'The position must be a string.',
            'position.max' => 'The position must not exceed 255 characters.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
        ];
        $validator = Validator::make($request->all(), $validationRules, $customMessages);

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
            'vendors_id' => 'required|integer|exists:vendors,id',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ];

        $customMessages = [
            'vendors_id.required' => 'The vendors ID field is required.',
            'vendors_id.integer' => 'The vendors ID must be an integer.',
            'vendors_id.exists' => 'The selected vendors ID does not exist in the database.',
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'position.required' => 'The position field is required.',
            'position.string' => 'The position must be a string.',
            'position.max' => 'The position must not exceed 255 characters.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
        ];
        $validator = Validator::make($request->all(), $validationRules, $customMessages);

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
