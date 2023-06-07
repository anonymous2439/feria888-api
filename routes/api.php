<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// authenticated routing
Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/user/get', [UserController::class, 'getUserInfo']);
    Route::post('/user/update', [UserController::class, 'updateUserInfo']);
    Route::post('/user/changepassword', [UserController::class, 'changePassword']);
    
    // protected admin routing
    Route::group(['middleware' => ['user.type']], function () {
        Route::get('/users', [UserController::class, 'show']);
        Route::get('/user/types', [UserTypeController::class, 'getUserTypes']);
        Route::delete('/user/{id}', [UserController::class, 'deleteUser']);
        Route::post('/user/add', [UserController::class, 'addUser']);
        Route::post('/user/edit/{id}', [UserController::class, 'editUser']);
    });
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

