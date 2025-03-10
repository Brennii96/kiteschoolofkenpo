<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Member;
use Illuminate\Auth\Events\Registered;
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
    public function loginView()
    {
        return (new View)
            ->template('auth.login')
            ->layout('layout');
    }


    /**
     * Display the registration view.
     */
    public function registerView()
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
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('member')->attempt($credentials)) {
            //            $request->member()->update([
            //                'last_login' => Carbon::now()->toDateTimeString(),
            ////                'last_login_ip' => $request->getClientIp()
            //            ]);
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
     * @throws ValidationException
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'lowercase', 'email', 'unique:' . Member::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $member = Member::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($member));

        Auth::guard('member')->login($member);

        return redirect("/members/profile");
    }

    /**
     * Destroy an authenticated session for members.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('member')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
