<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::all();

        return response()->json([
            'data' => $companies,
            'message' => 'Getting data companies successfully.',
        ], 200);
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
            'email' => 'required|string|email|max:255|unique:companies',
            'phone_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            // Add validation for other fields as needed
        ];

        $customMessages = [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'phone_number.required' => 'The phone number field is required.',
            'address.required' => 'The address field is required.',
            'city.required' => 'The city field is required.',
            'province.required' => 'The province field is required.',
            'postal_code.required' => 'The postal code field is required.',
            'country.required' => 'The country field is required.',
            'industry.required' => 'The industry field is required.',
            // Add custom messages for other fields as needed
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $company = Company::create($request->all());

        return response()->json([
            'status' => 201,
            'data' => $company,
            'message' => 'Company created successfully.',
        ], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        return response()->json([
            'status' => 200,
            'data' => $company,
            'message' => 'Getting data companies successfully.',
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $validationRules = [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:companies',
            'phone_number' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'province' => 'sometimes|string|max:255',
            'postal_code' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'industry' => 'sometimes|string|max:255',
            // Add validation for other fields as needed
        ];

        $customMessages = [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'email.unique' => 'The email has already been taken.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'city.string' => 'The city must be a string.',
            'city.max' => 'The city must not exceed 255 characters.',
            'province.string' => 'The province must be a string.',
            'province.max' => 'The province must not exceed 255 characters.',
            'postal_code.string' => 'The postal code must be a string.',
            'postal_code.max' => 'The postal code must not exceed 255 characters.',
            'country.string' => 'The country must be a string.',
            'country.max' => 'The country must not exceed 255 characters.',
            'industry.string' => 'The industry must be a string.',
            'industry.max' => 'The industry must not exceed 255 characters.',
            // Add custom messages for other fields as needed
        ];

        $validator = Validator::make($request->all(), $validationRules,$customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $company->update($request->all());

        return response()->json([
            'status' => 201,
            'data' => $company,
            'message' => 'Company updated successfully.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            'status' => 204,
            'data' => $company,
            'message' => 'Company deleted successfully.',
        ], 204);
    }

    /**
     * Get all companies owned by the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function owned()
    {
        $companies = Company::where('user_id', Auth::id())->get();

        return response()->json([
            'status' => 200,
            'data' => $companies,
        ]);
    }
}
