<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    //Create endpoin request
    public function register(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
            "role" => ['nullable']
        ]);

        //create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            // 'role'  => $validated['role']
        ]);
        //Create token
        $token = $user->createToken('auth_token')->plainTextToken;
        //Retun response json
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request) {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        $user = User::where('email',$validated['email'])->first();
        //if user wrong email or password
        if(!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'massage' => 'Email atau Password salah'
            ],401);
        }
        //if email and password correct
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    //Logout
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logout Berhasil'
        ]);

    }

    //Create me method to check user
    public function me(Request $request) {
        return response()->json($request->user());
    }
}
