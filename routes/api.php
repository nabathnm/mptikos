<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KosanApiController;
use App\Http\Controllers\Api\PemesananApiController;
use App\Http\Controllers\Api\UlasanApiController;
use App\Http\Controllers\Api\FasilitasApiController;
use App\Http\Controllers\Api\UsersApiController;

// =====================
// PUBLIC ROUTES
// =====================
Route::name('api.')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::apiResource('kosan', KosanApiController::class)->only(['index', 'show']);
    Route::apiResource('ulasan', UlasanApiController::class)->only(['index', 'show']);
    Route::apiResource('fasilitas', FasilitasApiController::class)->only(['index', 'show']);
});

// =====================
// PROTECTED ROUTES (JWT)
// =====================
Route::middleware('auth:api')->name('api.')->group(function () {
    // --- Auth ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- REST Resources ---
    Route::apiResource('kosan', KosanApiController::class)->except(['index', 'show']);
    Route::apiResource('pemesanan', PemesananApiController::class);
    Route::apiResource('ulasan', UlasanApiController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('fasilitas', FasilitasApiController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('users', UsersApiController::class);
});