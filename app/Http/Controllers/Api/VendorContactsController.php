<?php

namespace App\Http\Controllers\Api;

use App\Models\VendorContact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class VendorContactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $vendorContacts = VendorContact::with('vendor')->get();
        return response()->json([
            'status' => 200,
            'data' => $vendorContacts,
            'message' => 'Vendor contacts retrieved successfully.',
        ]);
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
        // Validate the request
        $validatedData = $request->validate($validationRules, $customMessages);

        // Create the vendor contact with validated data
        $vendorContact = VendorContact::create($validatedData);

        // Return response
        return response()->json([
            'status' => 201,
            'data' => $vendorContact,
            'message' => 'Vendor contact created successfully.',
        ], 201);
    }

    public function show($id)
    {
        $vendorContact = VendorContact::with('vendor')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $vendorContact,
            'message' => 'Vendor contact retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
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
        $validated = $request->validate([
            'vendors_id' => 'required|exists:vendors,id',
            'name' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        $vendorContact = VendorContact::findOrFail($id);
        $vendorContact->update($validated, $customMessages);
        return response()->json([
            'status' => 201,
            'data' => $vendorContact,
            'message' => 'Vendor contact updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $vendorContact = VendorContact::findOrFail($id);
        $vendorContact->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Vendor contact deleted successfully.',
        ]);
    }
}
