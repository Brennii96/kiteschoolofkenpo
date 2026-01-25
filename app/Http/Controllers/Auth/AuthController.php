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
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Statamic\View\View;

class AuthController extends Controller
{
    /**
     * Display the login view.
     */
    public function loginView(): View
    {
        return (new View)
            ->template('auth.login')
            ->layout('layout');
    }

    /**
     * Display the registration view.
     */
    public function registerView(): View
    {
        return (new View)
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

        /** @var StatefulGuard $memberGuard */
        $memberGuard = Auth::guard('member');

        if ($memberGuard->attempt($credentials, $request->boolean('remember'))) {
            /** @var Member $member */
            $member = $memberGuard->user();
            $member->update([
                'last_login' => Carbon::now()->toDateTimeString(),
            ]);

            $request->session()->regenerate();

            return redirect()->intended('/members/profile');
        }

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

            $existingMember->sendEmailVerificationNotification();

            return back()
                ->withInput($request->only('email', 'name'))
                ->with('status', 'A verification email has been sent. Please check your inbox.');
        }

        $member = Member::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($member));

        /** @var StatefulGuard $memberGuard */
        $memberGuard = Auth::guard('member');
        $memberGuard->login($member);

        return redirect('/members/profile');
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
