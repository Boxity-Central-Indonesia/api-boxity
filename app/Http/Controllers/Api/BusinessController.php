<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\businesses;
use App\Models\profiles;
use Illuminate\Support\Facades\Auth;


class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private function getProfileId()
    {
        $user = Auth::user();
        $profile = profiles::where('user_id', $user->id)->first();
        return $profile ? $profile->id : null;
    }

    public function index()
    {
        $profileId = $this->getProfileId();
        $businesses = businesses::where('profile_id', $profileId)->get();
        return response()->json([
            'status' => 200,
            'data' => $businesses,
        ]);
    }

    public function store(Request $request)
    {
        $profileId = $this->getProfileId();
        $request->validate([
            'nama_bisnis' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:businesses',
            'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

        $data = $request->all();
        if ($request->hasFile('business_logo')) {
            $result = cloudinary()->upload($request->file('business_logo')->getRealPath())->getSecurePath();
            $data['business_logo'] = $result;
        }

        $business = businesses::create($data + ['profile_id' => $profileId]);
        return response()->json([
            'status' => 201,
            'data' => $business,
        ], 201);
    }

    public function show($id)
    {
        $profileId = $this->getProfileId();
        $business = businesses::where('id', $id)->where('profile_id', $profileId)->first();
        if (!$business) {
            return response()->json([
                'status' => 404,
                'message' => 'Business not found',
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'data' => $business,
        ]);
    }

    public function update(Request $request, $id)
    {
        $profileId = $this->getProfileId();
        $business = businesses::where('id', $id)->where('profile_id', $profileId)->first();
        if (!$business) {
            return response()->json([
                'status' => 404,
                'message' => 'Business not found',
            ], 404);
        }
        $data = $request->all();
        if ($request->hasFile('business_logo')) {
            $result = cloudinary()->upload($request->file('business_logo')->getRealPath())->getSecurePath();
            $data['business_logo'] = $result;
        }

        $business->update($data);
        return response()->json([
            'status' => 200,
            'data' => $business,
        ]);
    }


    public function destroy($id)
    {
        $profileId = $this->getProfileId();
        $business = businesses::where('id', $id)->where('profile_id', $profileId)->first();
        if (!$business) {
            return response()->json([
                'status' => 404,
                'message' => 'Business not found',
            ], 404);
        }
        $business->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Business deleted',
        ]);
    }
}