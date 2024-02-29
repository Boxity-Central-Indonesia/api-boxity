<?php

namespace App\Http\Controllers\Api;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Events\formCreated;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $vendors = Vendor::all();
        // Mengubah nilai transaction_type
        $vendors = $vendors->map(function ($vendor) {
            $vendor->transaction_type = ($vendor->transaction_type === 'inbound') ? 'supplier' : 'customer';
            return $vendor;
        });
        return response()->json([
            'status' => 200,
            'data' => $vendors,
            'message' => 'Vendors retrieved successfully.',
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
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
            'date_of_birth' => 'nullable|date',
            'transaction_type' => 'required|in:outbound,inbound',
        ]);

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'date_of_birth.date' => 'Please provide a valid date of birth.',
            'transaction_type.required' => 'The transaction type field is required.',
            'transaction_type.in' => 'The transaction type must be either "outbound" or "inbound".',
        ];

        $vendor = Vendor::create($validated, $customMessages);
        broadcast(new formCreated('New Vendor created successfully.'));
        return response()->json([
            'status' => 201,
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
    public function show($id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $vendor,
            'message' => 'Vendor retrieved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'email' => 'required|email',
            'date_of_birth' => 'nullable|date',
            'transaction_type' => 'required|in:outbound,inbound',
        ]);
        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'date_of_birth.date' => 'Please provide a valid date of birth.',
            'transaction_type.required' => 'The transaction type field is required.',
            'transaction_type.in' => 'The transaction type must be either "outbound" or "inbound".',
        ];

        $vendor = Vendor::findOrFail($id);
        $vendor->update($validated, $customMessages);
        broadcast(new formCreated('New Vendor created successfully.'));
        return response()->json([
            'status' => 201,
            'data' => $vendor,
            'message' => 'Vendor updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        Vendor::destroy($id);
        return response()->json([
            'status' => 200,
            'message' => 'Vendor deleted successfully.',
        ]);
    }
}
