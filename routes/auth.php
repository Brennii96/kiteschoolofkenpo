<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Members\SubscriptionController;

Route::middleware('auth:member')->group(function () {
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('members.verification.verify');

    Route::post('/members/{member}/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/members/{member}/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('/members/{member}/resume', [SubscriptionController::class, 'resume']);
    Route::get('/members/{member}/subscription-status', [SubscriptionController::class, 'subscriptionStatus']);
    Route::post('/members/{member}/update-payment-method', [SubscriptionController::class, 'updatePaymentMethod']);
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
