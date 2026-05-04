<?php

use App\Http\Controllers\Auth\ApproveMemberController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Members\ProfileController;
use App\Http\Controllers\Members\SubscriptionController;
use App\Http\Controllers\Members\TrainingHallController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Statamic\View\View;

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/email/verify', function () {
    return (new View())
        ->template('auth.verify-email')
        ->layout('layout');
})->middleware('auth:member')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    try {
        $request->user('member')->sendEmailVerificationNotification();
    } catch (\Throwable $exception) {
        report($exception);

        return back()->withErrors([
            'email' => "We couldn't send the email right now. Please try again in a few minutes.",
        ]);
    }

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth:member', 'throttle:6,1'])->name('verification.send');

Route::get('/members/approve/{id}/{hash}', ApproveMemberController::class)
    ->name('member.approve');

Route::get('/members/pending-approval', [ProfileController::class, 'pendingApproval'])
    ->middleware(['auth:member', 'verified.member'])
    ->name('members.pending-approval');

Route::middleware(['auth:member', 'verified.member'])->group(function () {
    Route::get('/members/profile', [ProfileController::class, 'show'])->name('members.profile');
    Route::patch('/members/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/members/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/members/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth:member', 'verified.member', 'approved.member'])->group(function () {
    Route::get('/members/training-hall/{grade?}', TrainingHallController::class)->name('members.training-hall');
    Route::get('/members/choose-your-plan', [ProfileController::class, 'chooseYourPlan'])->name('members.choose-your-plan');

    Route::post('/members/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/members/cancel', [SubscriptionController::class, 'cancel']);
    Route::post('/members/resume', [SubscriptionController::class, 'resume']);
    Route::get('/members/subscription-status', [SubscriptionController::class, 'subscriptionStatus']);
    Route::post('/members/update-payment-method', [SubscriptionController::class, 'updatePaymentMethod']);
    Route::get('/members/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('members.billing-portal');
});

Route::get('/login', [AuthController::class, 'loginView'])
    ->name('login.view')->middleware('guest:member');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')->middleware('guest:member');

Route::post('/logout', [AuthController::class, 'logout'])
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
