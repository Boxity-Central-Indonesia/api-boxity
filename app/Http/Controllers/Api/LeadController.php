<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LeadRequest;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Events\formCreated;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::all();
        return response()->json([
            'status' => 200,
            'data' => $leads,
            'message' => 'Leads retrieved successfully.',
        ]);
    }

    public function store(LeadRequest $request)
    {
        $lead = Lead::create($request->all());
        broadcast(new formCreated('New Lead created successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $lead,
            'message' => 'Lead created successfully.',
        ]);
    }

    public function show($id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $lead,
            'message' => 'Lead retrieved successfully.',
        ]);
    }

    public function update(LeadRequest $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $lead->update($request->all());
        broadcast(new formCreated('Lead updated successfully.'));

        return response()->json([
            'status' => 201,
            'data' => $lead,
            'message' => 'Lead updated successfully.',
        ]);
    }

    public function destroy($id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $lead->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Lead deleted successfully.',
        ]);
    }
}
