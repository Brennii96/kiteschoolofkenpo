<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Statamic\View\View;
use Throwable;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function loginView(): View
    {
        return (new View())
            ->template('auth.login')
            ->layout('layout');
    }

    /**
     * Display the registration view.
     */
    public function registerView(): View
    {
        return (new View())
            ->template('auth.register')
            ->layout('layout');
    }

    /**
     * Handle an incoming authentication request for members.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ])->withInput($request->only('email'));
        }

        /** @var StatefulGuard $memberGuard */
        $memberGuard = Auth::guard('member');

        if ($memberGuard->attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);

            /** @var Member $member */
            $member = $memberGuard->user();
            $member->update([
                'last_login' => Carbon::now()->toDateTimeString(),
            ]);

            $request->session()->regenerate();

            return redirect()->intended('/members/profile');
        }

        RateLimiter::hit($throttleKey);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle an incoming registration request for members.
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'lowercase', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $existingMember = Member::where('email', $request->input('email'))->first();

        if ($existingMember) {
            if ($existingMember->hasVerifiedEmail()) {
                throw ValidationException::withMessages([
                    'email' => 'This email is already registered.',
                ]);
            }

            try {
                $existingMember->sendEmailVerificationNotification();
            } catch (Throwable $exception) {
                report($exception);

                return back()
                    ->withInput($request->only('email', 'name'))
                    ->withErrors([
                        'email' => "We couldn't send the email right now. Please try again in a few minutes.",
                    ]);
            }

            return back()
                ->withInput($request->only('email', 'name'))
                ->with('status', 'A verification email has been sent. Please check your inbox.');
        }

        $member = Member::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $notificationFailed = false;

        try {
            event(new Registered($member));
        } catch (Throwable $exception) {
            report($exception);

            $notificationFailed = true;
        }

        /** @var StatefulGuard $memberGuard */
        $memberGuard = Auth::guard('member');
        $memberGuard->login($member);

        $redirect = redirect()->route('members.pending-approval');

        if ($notificationFailed) {
            return $redirect->withErrors([
                'email' => "Your account was created, but we couldn't send the email right now. Please try again from your account page.",
            ]);
        }

        return $redirect;
    }

    /**
     * Destroy an authenticated session for members.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
