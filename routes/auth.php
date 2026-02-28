<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Members\ProfileController;
use App\Http\Controllers\Members\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware('auth:member')->group(function () {

    Route::get('/members/profile', [ProfileController::class, 'show'])->name('members.profile');
    Route::get('/members/choose-your-plan', [ProfileController::class, 'chooseYourPlan'])->name('members.choose-your-plan');
    Route::patch('/members/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/members/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/members/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
