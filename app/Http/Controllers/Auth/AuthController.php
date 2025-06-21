<?php

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
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        /** @var StatefulGuard $memberGuard */
        $memberGuard = Auth::guard('member');
        if ($memberGuard->attempt($credentials)) {
            /** @var Member $member */
            $member = $memberGuard->user();
            $member->update([
                'last_login' => Carbon::now()->toDateTimeString(),
//                'last_login_ip' => $request->getClientIp()
            ]);
            $request->session()->regenerate();
            return redirect()->intended('/members/profile');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle an incoming registration request for members.
     *
     * @param Request $request
     * @return RedirectResponse
     *
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'lowercase', 'email', 'unique:' . Member::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $member = Member::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        event(new Registered($member));
        /** @var StatefulGuard $memberGuard */
        $memberGuard = Auth::guard('member');
        $memberGuard->login($member);

        return redirect("/members/profile");
    }

    /**
     * Destroy an authenticated session for members.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('member')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
