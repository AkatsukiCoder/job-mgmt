<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'pong';
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::prefix('jobs')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [JobPostingController::class, 'index']);
    Route::post('/', [JobPostingController::class, 'store']);
    Route::put('/{jobPosting}', [JobPostingController::class, 'update']);
    Route::get('/{jobPosting}', [JobPostingController::class, 'show']);
});
