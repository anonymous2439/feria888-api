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
                    ->select('users.email', 'user_types.name as type', 'users.username', 'users.phone_number', 'users.id')
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
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'phone_number' => 'required|string',
        ]);

        // Create a new user record
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone_number' => $data['phone_number'],
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

    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();

        // Retrieve and validate the input data
        $data = $request->validate([
            'email' => 'string',
            'phone_number' => 'string',
        ]);

        // Update the user record
        $user->email = $data['email'] ?? $user->email;
        $user->phone_number = $data['phone_number'] ?? $user->phone_number;
        $user->save();

        return response()->json(['message' => 'User information updated', 'user' => $user]);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Validate the input data
        $data = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|confirmed',
        ]);

        // Check if the current password matches
        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        // Update the password
        $user->password = bcrypt($data['new_password']);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        
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
