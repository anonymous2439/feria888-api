<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function show()
    {
        $users = DB::table('users')
                    ->join('user_types', 'users.type_id', '=', 'user_types.id')
                    ->select('users.email', 'user_types.name as type')
                    ->get();
        return $users;
    }

    public function getUserInfo(Request $request)
    {
        $user = Auth::user();
        return $user;
    }
    
    public function register(Request $request)
    {
        // Retrieve and validate the input data
        $data = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        // Create a new user record
        $user = User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'type_id' => 1,
        ]);

        $token = $user->createToken('feria888token')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];

        // Return a response or redirect as needed
        return response()->json($response);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Retrieve the authenticated user
            $token = $user->createToken('feria888token')->plainTextToken;
            // Authentication successful
            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ]);
        }
        
        // Authentication failed
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }
}
