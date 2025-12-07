<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdaPiDeviceStatusController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group.
|
*/

// Existing Sanctum route (Laravel default)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| ADA-Pi Authentication Routes (JWT)
|--------------------------------------------------------------------------
*/

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected auth routes (JWT)
Route::middleware('auth:api')->prefix('auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validate', [AuthController::class, 'validateToken']);
});

/*
|--------------------------------------------------------------------------
| ADA-Pi Device Routes (JWT authenticated)
|--------------------------------------------------------------------------
|
| Pi-ul cheamă:
|   POST https://www.adasystems.uk/api/ada-pi/device/status
|
| Protejat cu middleware ada-pi.jwt care verifică:
|   - Authorization: Bearer <token>
|   - Token semnat cu ADA_PI_JWT_SECRET
|   - device_id din token = device_id din body
|
*/

Route::prefix('ada-pi')->middleware('ada-pi.jwt')->group(function () {
    Route::post('/device/status', [AdaPiDeviceStatusController::class, 'status']);
});
