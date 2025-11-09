<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'pong';
});

Route::middleware(['throttle:60,1', 'auth:sanctum', 'request.log'])->group(function () {
    Route::prefix('jobs')->group(function () {
        Route::get('/', [JobPostingController::class, 'index']);
        Route::post('/', [JobPostingController::class, 'store']);
        Route::put('/{jobPosting}', [JobPostingController::class, 'update']);
        Route::get('/{jobPosting}', [JobPostingController::class, 'show']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['throttle:60,1', 'request.log'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });
});
