<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Lead;

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

    public function store(Request $request)
    {
        $request->validate([
            'nama_prospek' => 'required',
            'email_prospek' => 'required|email|unique:leads',
            'nomor_telepon_prospek' => 'nullable',
            'tipe_prospek' => 'required|in:perorangan,bisnis,rekomendasi',
        ]);

        $lead = Lead::create($request->all());

        return response()->json([
            'status' => 200,
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

    public function update(Request $request, $id)
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        $request->validate([
            'nama_prospek' => 'required',
            'email_prospek' => 'required|email|unique:leads,email_prospek,' . $lead->id,
            'nomor_telepon_prospek' => 'nullable',
            'tipe_prospek' => 'required|in:perorangan,bisnis,rekomendasi',
        ]);

        $lead->update($request->all());

        return response()->json([
            'status' => 200,
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
