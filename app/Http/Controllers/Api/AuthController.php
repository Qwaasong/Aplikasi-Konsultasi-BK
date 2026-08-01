<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator -> fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator -> errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user -> createToken('AuthToken')->accessToken;

        return response() -> json([
            'status' => True,
            'message' => 'Register berhasil!',
            'data' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $token = $user->createToken('AuthToken')->accessToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil!',
            'data'    => $user,
            'token'   => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $accessToken = $user->token();

        if ($accessToken instanceof \Laravel\Passport\AccessToken) {
            $accessToken->revoke();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil!',
        ], 200);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data'   => $request->user()
        ], 200);
    }
}
