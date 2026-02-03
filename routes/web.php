<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\UserController;

// Route Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group Route yang butuh Login
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () { return view('pages.index'); });
    Route::resource('users', UserController::class);
    Route::resource('siswas', SiswaController::class);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});