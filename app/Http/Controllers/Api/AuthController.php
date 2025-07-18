<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request){

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
       
        $user = User::where('email', $request->email)->first();

        if(! $user || ! Hash::check($request->password, $user->password)){
            return response()->json([
            'message' => 'Invalid credentials.'
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successfull',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ],200);

    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out Successfully'
        ]);
    }
}
