<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use App\Http\Controllers\Auth\PasswordResetController;


// --------------Auth Routes
Route::controller(AuthController::class)->group(function () {
    //Route::post('register', 'register');
    Route::post('login', 'login');
    // Route::post('logout', 'logout')->middleware('auth:Sanctum');
});
//  All Routes need token
Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Example for admin
    Route::middleware('role:admin')->get('admin/dashboard', function () {
        return response()->json(['message' => 'Welcome admin']);
    });
});
//-----------------Forget and rest password
Route::post('/forget-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
