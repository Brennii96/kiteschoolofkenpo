<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;

//Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
//    ->middleware(['auth', 'signed', 'throttle:6,1'])
//    ->name('verification.verify');

Route::middleware('auth:member')->group(function () {
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('members.verification.verify');
});

Route::get('/login', [AuthController::class, 'loginView'])
    ->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')->middleware('guest');

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth:member');

Route::get('/register', [AuthController::class, 'registerView'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest');
