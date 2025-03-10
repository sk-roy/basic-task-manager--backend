<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) 
    {
        $registeredData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required'
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'admin' => false,
        ]);
    
        $accessToken = $user->createToken('authToken')->accessToken;
    
        return response()->json([
            'messge' => 'Registration success.',
            'user' => $user,
            'token' => $accessToken,
        ]);
    }

    public function login(Request $request) 
    {        
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'Invalid Credentials!',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // $cookie = cookie('auth_token', $token, 60*24); // 1 day
 
        return response([
            'messge' => 'Success',
            'token' => $token,
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function checkAuth(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                'authenticated' => true,
                'user' => $request->user(),
            ]);
        }

        return response()->json([
            'authenticated' => false,
        ], 401);
    }

}
