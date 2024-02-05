<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CompanyRequest;
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
            'status' => 200,
            'data' => $companies,
            'message' => 'Getting data companies successfully.',
        ], 200);
    }

    public function store(CompanyRequest $request)
    {
        $company = Company::create($request->validated());
        return response()->json([
            'status' => 201,
            'data' => $company,
            'message' => 'Company created successfully.',
        ], 201);
    }

    public function update(CompanyRequest $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->validated());
        return response()->json([
            'status' => 200,
            'data' => $company,
            'message' => 'Company updated successfully.',
        ], 200);
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
