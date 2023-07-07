<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\CoinsController;
use App\Http\Controllers\WalletsController;
use App\Http\Controllers\AgentController;

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
    Route::get('/agent/online', [AgentController::class, 'getAllOnlineAgents']);
    
    // protected admin and agent routing
    Route::group(['middleware' => ['type.allowed:admin,agent']], function () {        
        Route::get('/user/search/{id}', [UserController::class, 'searchUser']);
        Route::post('/wallet/deduct', [WalletsController::class, 'deductWallet']);
        Route::post('/coins/load', [CoinsController::class, 'loadCoins']);
    });

    // protected admin routing
    Route::group(['middleware' => ['type.allowed:admin']], function () {
        Route::get('/users', [UserController::class, 'show']);        
        Route::get('/user/types', [UserTypeController::class, 'getUserTypes']);
        Route::delete('/user/delete/{id}', [UserController::class, 'deleteUser']);
        Route::post('/user/add', [UserController::class, 'addUser']);
        Route::post('/user/edit/{id}', [UserController::class, 'editUser']);
        Route::post('/user/changepassword/{id}', [UserController::class, 'changeUserPassword']);
        Route::get('/users/all', [UserController::class, 'getUsersWithCoinsAndWallets']);
        Route::get('/agents/all', [UserController::class, 'getAgents']);
        Route::post('/wallet/load', [WalletsController::class, 'loadWallet']);    
        Route::post('/agent/store', [AgentController::class, 'storeOrUpdateAgent']);            
    });

    // protected agent routing
    Route::group(['middleware' => ['type.allowed:agent']], function () {        
        Route::get('/agent/info', [AgentController::class, 'getAgentInfo']);
    });

    
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


