<?php

use App\Http\Controllers\JobPostingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'pong';
});

Route::prefix('jobs')->group(function () {
    Route::get('/', [JobPostingController::class, 'index']);
    Route::get('/{id}', [JobPostingController::class, 'show']);
});
