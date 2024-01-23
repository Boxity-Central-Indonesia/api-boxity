<?php

namespace App\Http\Controllers\Api;

use App\Models\CompaniesBranch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CompaniesBranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = CompaniesBranch::with('company')->get();

        return response()->json([
            'status' => 200,
            'data' => $branches,
            'message' => 'Branches retrieved successfully.',
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
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'company_id' => 'required|integer|exists:companies,id',
        ];

        $customMessages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'company_id.required' => 'The company ID field is required.',
            'company_id.integer' => 'The company ID must be an integer.',
            'company_id.exists' => 'The selected company ID does not exist in the database.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $branch = CompaniesBranch::create($request->all());

        return response()->json([
            'status' => 201,
            'data' => $branch,
            'message' => 'Branch created successfully.',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CompaniesBranch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show($company, $branch)
    {
        $branchModel = CompaniesBranch::find($branch);

        if (!$branchModel) {
            return response()->json(['message' => 'Branch not found.'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $branchModel,
            'message' => 'Branch retrieved successfully.',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CompaniesBranch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $company, $branch)
    {
        $validationRules = [
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255',
            'company_id' => 'sometimes|integer|exists:companies,id',
        ];

        $customMessages = [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not exceed 255 characters.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address must not exceed 255 characters.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number must not exceed 255 characters.',
            'email.string' => 'The email must be a string.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'The email must not exceed 255 characters.',
            'company_id.integer' => 'The company ID must be an integer.',
            'company_id.exists' => 'The selected company ID does not exist in the database.',
        ];

        $validator = Validator::make($request->all(), $validationRules,$customMessages);

        if ($validator->fails()) {
            Log::info('Validation errors:', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }

        $branchModel = CompaniesBranch::find($branch);

        if (!$branchModel) {
            return response()->json(['message' => 'Branch not found.'], 404);
        }

        $branchModel->update($request->all());

        return response()->json([
            'status' => 201,
            'data' => $branchModel,
            'message' => 'Branch updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CompaniesBranch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy($company, CompaniesBranch $branch)
    {
        $branch->delete();

        return response()->json(null, 204);
    }
}
