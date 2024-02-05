<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CompanyBranchRequest;
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

    public function store(CompanyBranchRequest $request)
    {
        $data = $request->validated();
        $branch = CompaniesBranch::create($data);
        return response()->json([
            'status' => 201,
            'data' => $branch,
            'message' => 'Branch created successfully.',
        ], 201);
    }

    public function update(CompanyBranchRequest $request, $branch)
    {
        $branchModel = CompaniesBranch::findOrFail($branch);
        $data = $request->validated();
        $branchModel->update($data);
        return response()->json([
            'status' => 200,
            'data' => $branchModel,
            'message' => 'Branch updated successfully.',
        ]);
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
