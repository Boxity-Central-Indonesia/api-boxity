<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MasterUsersController extends Controller
{
    public function index()
    {
        $user = User::all();

        if ($user) {
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
    public function show(User $user) // Tambahkan method show untuk menampilkan detail user
    {
        return response()->json([
            'status' => 200,
            'data' => $user
        ]);
    }

    public function store(Request $request)
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
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->errors()
                ]);
            }

            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'username'      => $request->username,
                'no_handphone'  => $request->no_handphone,
                'gender'        => $request->gender,
                'password'      => Hash::make($request->password)
            ]);

            if ($user) {
                return response()->json([
                    'status' => 201,
                    'data'=> $user,
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

    public function update(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,'.$id,
            'username'      => 'required|string|unique:users,username,'.$id,
            'no_handphone'  => 'required',
            'gender'        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Perbarui data pengguna
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->no_handphone = $request->no_handphone;
        $user->gender = $request->gender;
        $user->save();

        return response()->json([
            'status' => 201,
            'message' => 'User data updated successfully',
            'data' => $user
        ]);

    } catch (\Throwable $th) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $th->getMessage()
        ], 400);
    }
}

    public function destroy(User $user) // Tambahkan method destroy untuk menghapus user
    {
        try {
            $user->delete();
            return response()->json([
                'status' => 200,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 400);
        }
    }
    public function me()
    {
        if (!Auth::user()) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 200,
            'data' => Auth::user(),
        ]);
    }
}
