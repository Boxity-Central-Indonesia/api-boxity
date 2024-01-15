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
        $branches = CompaniesBranch::all();

        return response()->json([
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
            'email' => 'required|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
        ];

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $branch = CompaniesBranch::create($request->all());

        return response()->json([
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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'company_id' => 'required|integer|exists:companies,id',
        ];
        $validator = Validator::make($request->all(), $validationRules);

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
