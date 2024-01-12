<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MasterUsersController extends Controller
{
    public function showUserMaster()
    {
        $user = User::all();

        if($user){
            return response()->json([
                'status' => 200,
                'data' => $user,
            ]);
        }

        return response()->json([
            'status' => 400,
            'data' => null
        ]);

    }

    public function create(Request $request)
    {
       try {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|unique:users',
            'no_handphone' => 'required',
            'gender' => 'required',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'username'      => $request->username,
            'no_handphone'  => $request->no_handphone,
            'gender'        => $request->gender,
            'password'      => Hash::make($request->password)
        ]);

        if($user) {
            return response()->json([
                'status' => 201,
                'message' => 'data user berhasil di tambahkan'
            ]);
        }

       } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 400);
       }
    }

    public function read(Request $request)
    {
        $user = User::where('id', $request->id)->first();

        if($user) {
            return response()->json([
                'status' => 200,
                'data' => $user
            ]);
        }

        return response()->json([
            'status' => 400,
            'message' =>'error'
        ]);
    }
}
