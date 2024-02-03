<?php

namespace App\Http\Controllers\Api;

use App\Models\profiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = profiles::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => 200,
            'data' => $profiles,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['user_id' => Auth::id()]); // Menambahkan user_id ke request
        $profile = profiles::create($request->all());
        return response()->json([
            'status' => 201,
            'data' => $profile,
        ], 201);
    }

    public function show()
    {
        $profile = profiles::where('user_id', Auth::id())->first();
        if (!$profile) {
            return response()->json([
                'status' => 404,
                'message' => 'Profile not found',
            ], 404);
        }
        return response()->json([
            'status' => 200,
            'data' => $profile,
        ]);
    }

    public function update(Request $request, $id)
    {
        $profile = profiles::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$profile) {
            return response()->json([
                'status' => 404,
                'message' => 'Profile not found',
            ], 404);
        }
        $profile->update($request->all());
        return response()->json([
            'status' => 200,
            'data' => $profile,
        ]);
    }

    public function destroy()
    {
        $profile = profiles::where('user_id', Auth::id())->first();
        if (!$profile) {
            return response()->json([
                'status' => 404,
                'message' => 'Profile not found',
            ], 404);
        }
        $profile->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Profile deleted',
        ]);
    }
}
