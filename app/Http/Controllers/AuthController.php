<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request['email'])->first();

        if ($user && Hash::check($request['password'], $user->password)) {
            return response([
                'access_token' => $user->createToken('api')->plainTextToken,
                'token_type' => 'bearer',
                'expires_in' => config('sanctum.expiration') * 60,
            ]);
        }

        return response([
            'message' => 'Unauthorized',
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Successfully logged out.',
        ]);
    }

    public function user(Request $request)
    {
        return $request->user();
    }
}
