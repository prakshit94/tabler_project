<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\User\UserController;

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

// Public Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/otp', [OTPController::class, 'requestOtp']);
    Route::post('/verify-otp', [OTPController::class, 'verifyOtp']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });

    // User Management API
    Route::middleware('permission:MANAGE_USERS')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole']);
        Route::post('/users/{user}/remove-role', [UserController::class, 'removeRole']);
    });

    // Roles API
    Route::middleware('permission:MANAGE_ROLES')->group(function () {
        Route::apiResource('roles', \App\Http\Controllers\Role\RoleController::class);
        Route::post('/roles/{role}/permissions', [\App\Http\Controllers\Role\RoleController::class, 'assignPermissions']);
    });

    // Permissions API
    Route::middleware('permission:MANAGE_PERMISSIONS')->group(function () {
        Route::apiResource('permissions', \App\Http\Controllers\Permission\PermissionController::class);
    });

});
