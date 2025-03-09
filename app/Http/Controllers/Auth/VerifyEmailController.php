<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MemberEmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(MemberEmailVerificationRequest $request): RedirectResponse
    {
        $member = $request->member();

        if ($member && $member->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url') . '/members/dashboard?verified=1'
            );
        }

        if ($member && $member->markEmailAsVerified()) {
            event(new Verified($member));
        }

        return redirect()->intended(
            config('app.frontend_url') . '/members/dashboard?verified=1'
        );
    }
}
