<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FirebaseOrderController;
use App\Http\Controllers\Api\FirebaseLiveTrackingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppUserController;
use App\Http\Controllers\Api\FirebaseUserController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/firebase/users', [FirebaseUserController::class, 'index']);
    Route::get('/firebase/orders', [FirebaseOrderController::class, 'index']);

    // Live tracking endpoints
    Route::get('/firebase/live-tracking', [FirebaseLiveTrackingController::class, 'index']);
    Route::get('/firebase/drivers/{driverId}/location', [FirebaseLiveTrackingController::class, 'getDriverLocation']);
    Route::post('/firebase/drivers/locations', [FirebaseLiveTrackingController::class, 'batchDriverLocations']);
});

// SQL users listing (replaces client-side Firebase usage on Users page)
Route::get('/app-users', [AppUserController::class, 'index']);
Route::post('/app-users', [AppUserController::class, 'store']);
Route::delete('/app-users/{id}', [AppUserController::class, 'destroy']);
Route::patch('/app-users/{id}/active', [AppUserController::class, 'setActive']);

Route::post('/send-otp', [App\Http\Controllers\OTPController::class, 'sendOtp']);
Route::post('/verify-otp', [App\Http\Controllers\OTPController::class, 'verifyOtp']);
Route::post('/resend-otp', [App\Http\Controllers\OTPController::class, 'resendOtp']);
Route::post('/sms-delivery-status', [App\Http\Controllers\OTPController::class, 'smsDeliveryStatus']);

// Debug route - remove in production
Route::get('/debug-otp/{phone}', [App\Http\Controllers\OTPController::class, 'debugOtp']);

