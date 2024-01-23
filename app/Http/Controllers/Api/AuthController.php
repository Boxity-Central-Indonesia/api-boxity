<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Validator;
use App\Notifications\WelcomeEmailNotification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // return $request;
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name must be a string.',
                'name.max' => 'The name must not exceed 255 characters.',
                'email.required' => 'The email field is required.',
                'email.string' => 'The email must be a string.',
                'email.email' => 'Please provide a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'The provided email is already in use.',
                'password.required' => 'The password field is required.',
                'password.string' => 'The password must be a string.',
                'password.min' => 'The password must be at least 8 characters.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);


            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password)
            ]);


            $user->notify(new WelcomeEmailNotification($user));

            $token = $user->createToken('auth_token')->plainTextToken;

            setcookie('bearerToken', $token, 0, '/', null, false, true);

            return response()->json([
                'status' => 201,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 200);
        }
    }

    public function otp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $user = User::where('email', $request->email)->first();

            $otp = rand(100000, 999999);

            $user->update([
                'token' => $otp,
                'token_expires_at' => now()->addMinutes(5)
            ]);

            $user->notify(new OtpNotification($user));

            return response()->json([
                'message' => 'OTP sent to your email'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email',
                'password' => 'required|string|min:8',
            ], [
                'email.required' => 'The email field is required.',
                'email.string' => 'The email must be a string.',
                'email.email' => 'Please provide a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.exists' => 'The provided email does not exist.',
                'password.required' => 'The password field is required.',
                'password.string' => 'The password must be a string.',
                'password.min' => 'The password must be at least 8 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 400, 'message' => $validator->errors()]);
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Password mismatch'
                ], 400);
            }

            // if ($user->token != $request->otp) {
            //     return response()->json([
            //         'message' => 'OTP mismatch'
            //     ], 401);
            // }

            // if ($user->token_expires_at < now()) {
            //     return response()->json([
            //         'message' => 'OTP expired'
            //     ], 401);
            // }

            // $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            setcookie('bearerToken', $token, 0, '/', null, false, true);

            return response()->json([
                'status' => 200,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            return response()->json(new UserResource($request->user()));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
            ], [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name must be a string.',
                'name.max' => 'The name must not exceed 255 characters.',
                'email.required' => 'The email field is required.',
                'email.string' => 'The email must be a string.',
                'email.email' => 'Please provide a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',
                'email.unique' => 'The provided email is already in use.',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            }

            $request->user()->update($request->only('name', 'email'));

            return response()->json(new UserResource($request->user()));
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'message' => 'Logged out'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
