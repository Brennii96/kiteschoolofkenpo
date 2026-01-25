<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MemberEmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(MemberEmailVerificationRequest $request): RedirectResponse
    {
        $member = $request->member();

        if ($member->hasVerifiedEmail()) {
            Auth::guard('member')->login($member);

            return redirect()->route('members.profile')
                ->with('status', 'Your email is already verified.');
        }

        if ($member->markEmailAsVerified()) {
            event(new Verified($member));
        }

        Auth::guard('member')->login($member);

        return redirect()->route('members.choose-your-plan')
            ->with('status', 'Your email has been verified! Please choose a plan to get started.');
    }
}
