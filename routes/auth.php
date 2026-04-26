<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Members\ProfileController;
use App\Http\Controllers\Members\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/email/verify', function () {
    return (new \Statamic\View\View())
        ->template('auth.verify-email')
        ->layout('layout');
})->middleware('auth:member')->name('verification.notice');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user('member')->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth:member', 'throttle:6,1'])->name('verification.send');

Route::middleware(['auth:member', 'verified.member'])->group(function () {

    Route::get('/members/profile', [ProfileController::class, 'show'])->name('members.profile');
    Route::get('/members/choose-your-plan', [ProfileController::class, 'chooseYourPlan'])->name('members.choose-your-plan');
    Route::patch('/members/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/members/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/members/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/members/{member}/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/members/{member}/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('/members/{member}/resume', [SubscriptionController::class, 'resume']);
    Route::get('/members/{member}/subscription-status', [SubscriptionController::class, 'subscriptionStatus']);
    Route::post('/members/{member}/update-payment-method', [SubscriptionController::class, 'updatePaymentMethod']);
});

Route::get('/login', [AuthController::class, 'loginView'])
    ->name('login.view')->middleware('guest:member');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')->middleware('guest:member');

Route::get('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth:member');

Route::get('/register', [AuthController::class, 'registerView'])
    ->middleware('guest:member')
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest:member');

Route::middleware('guest:member')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'forgotPasswordView'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetView'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});
