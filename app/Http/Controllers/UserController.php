<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function show(string $id)
    {
        $users = DB::select('select * from users');
        return $users;
    }
    
    public function register(Request $request)
    {
        // Retrieve and validate the input data
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        // Create a new user record
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
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
            // Authentication successful
            return response()->json(['message' => 'Login successful']);
        }
        
        // Authentication failed
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
}
