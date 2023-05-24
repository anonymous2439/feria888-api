<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function show(string $id)
    {
        $users = DB::select('select * from users where id = ?', [$id]);
        return $users;
    }
}
