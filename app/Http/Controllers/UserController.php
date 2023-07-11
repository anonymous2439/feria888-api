<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserType;
use App\Models\Agent;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show()
    {
        $users = DB::table('users')
                    ->join('user_types', 'users.type_id', '=', 'user_types.id')
                    ->select('users.email', 'user_types.name as type', 'user_types.id as type_id', 'users.username', 'users.phone_number', 'users.id')
                    ->get();
        return $users;
    }

    public function getUserInfo(Request $request)
    {
        $user = Auth::user();
        $user->load(['coins' => function ($query) {
            $query->latest()->first();
        }, 'wallets' => function ($query) {
            $query->latest()->first();
        } ,'userType']);
        return $user;
    }

    public function searchUser(Request $request, $search)
    {
        $user = User::where(function ($query) use ($search) {
            $query->where('id', $search)
                ->orWhere('username', $search);
        })->first();
        $user->load(['coins' => function ($query) {
            $query->latest()->first();
        }, 'wallets' => function ($query) {
            $query->latest()->first();
        } ,'userType']);
        return $user;
    }
    
    public function register(Request $request)
    {
        try {
            // Retrieve and validate the input data
            $data = $request->validate([
                'username' => 'required|string|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|confirmed',
                'phone_number' => 'required|string',
            ]);

            // Get all user types
            $userType = UserType::where('name', 'user')->first();

            // Create a new user record
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'phone_number' => $data['phone_number'],
                'type_id' => $userType->id,
            ]);

            $token = $user->createToken('feria888token')->plainTextToken;
            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response()->json([
                'message' => 'Registration successful',
                'data' => $response,
                'success' => 1
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'success' => 0
            ]);
        }
    }

    public function addUser(Request $request)
    {
        // Retrieve and validate the input data
        $data = $request->validate([
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'phone_number' => 'required|string',
            'type_id' => 'required|int',
        ]);

        // Create a new user record
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'phone_number' => $data['phone_number'],
            'type_id' => $data['type_id'],
        ]);

        if ($user->userType->name === 'agent') {
            // Store a new row for the Agent model
            Agent::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'offline', 'link' => '']
            );
        }

        $token = $user->createToken('feria888token')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];

        // Return a response or redirect as needed
        return response()->json($response);
    }


    public function editUser(Request $request, $id)
    {
        // Retrieve and validate the input data
        $data = $request->validate([
            'username' => 'required|string|unique:users,username,'.$id,
            'email' => 'required|email|unique:users,email,'.$id,
            'phone_number' => 'required|string',
            'type_id' => 'required|int',
        ]);

        // Find the user to be updated
        $user = User::findOrFail($id);

        // Update the user record
        $user->update([
            'username' => $data['username'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'type_id' => $data['type_id'],
        ]);

        if ($user->userType->name === 'agent') {
            // Store a new row for the Agent model
            Agent::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'offline', 'link' => '']
            );
        }

        // Return the updated user
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
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

        $user->load(['coins' => function ($query) {
            $query->latest()->first();
        }, 'wallets' => function ($query) {
            $query->latest()->first();
        } ,'userType']);

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

    public function changeUserPassword(Request $request, $id)
    {
        // Find the user to be updated
        $user = User::findOrFail($id);

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

        if ($user && $user->userType->name != 'root') {
            $user->delete();            
        }
        else {
            return response()->json(['message' => 'User not found'], 404);
        }

        

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Retrieve the authenticated user
            $user->load(['coins' => function ($query) {
                $query->latest()->first();
            }, 'wallets' => function ($query) {
                $query->latest()->first();
            } ,'userType']);
            $token = $user->createToken('feria888token')->plainTextToken;
            // Authentication successful
            return response()->json([
                'success' => 1,
                'user' => $user,
                'token' => $token
            ]);
        }
        
        // Authentication failed
        return response()->json([
            'message' => 'Invalid credentials',
            'success' => 0
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    // get all user with coins and wallets
    public function getUsersWithCoinsAndWallets()
    {
        $users = User::with(['coins' => function ($query) {
            $query->latest();
        }, 'userType', 'wallets' => function ($query) {
            $query->latest()->first();
        }])->get();

        return response()->json($users);
    }

    // get all agents
    public function getAgents()
    {
        $users = User::with(['coins' => function ($query) {
            $query->latest()->first();
        }, 'userType', 'wallets' => function ($query) {
            $query->latest()->first();
        }])->whereHas('userType', function ($query) {
            $query->where('name', 'agent');
        })->get();

        return response()->json($users);
    }
}
