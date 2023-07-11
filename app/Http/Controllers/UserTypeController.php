<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;

class UserTypeController extends Controller
{
    public function getUserTypes()
    {
        $userTypes = UserType::all()->where('name', '!=', 'root');
        return response()->json($userTypes);
    }
}

