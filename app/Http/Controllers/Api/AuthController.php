<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            // Get the authenticated user
            $user = Auth::user();

            // Generate a token for the user using Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);
        }

        // Return error if authentication fails
        return response()->json([
            'success' => false,
            'message' => 'Invalid login credentials',
        ], 401);
    }
    public function register(Request $request)
    {
        // Validate the incoming user data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // 'confirmed' means it will check for a 'password_confirmation' field
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the new user in the database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), // Securely hash the password
            'role' => 'user', // Default role for new signups
            'total_points' => 0, // Initial points for gamification
        ]);

        // Generate an access token for the newly registered user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }
}