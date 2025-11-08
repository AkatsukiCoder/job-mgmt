<?php

use App\Http\Controllers\AuthSessionController;
use App\Http\Controllers\JobPageController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/jobs');

Route::get('/login', [AuthSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthSessionController::class, 'store'])->name('login.store');
Route::post('/logout', [AuthSessionController::class, 'destroy'])->name('logout');

Route::get('/jobs', [JobPageController::class, 'index'])->name('jobs.index');
Route::get('/jobs/create', [JobPageController::class, 'create'])->name('jobs.create');
Route::post('/jobs', [JobPageController::class, 'store'])->name('jobs.store');
Route::get('/jobs/{jobId}/edit', [JobPageController::class, 'edit'])->name('jobs.edit');
Route::put('/jobs/{jobId}', [JobPageController::class, 'update'])->name('jobs.update');
