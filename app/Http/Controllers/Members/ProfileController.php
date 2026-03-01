<?php

declare(strict_types=1);

namespace App\Http\Controllers\Members;

use App\Contracts\StripeServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Statamic\View\View;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly StripeServiceInterface $stripeService
    ) {}

    public function show(): View
    {
        $member = Auth::guard('member')->user();

        return (new View)
            ->template('members.profile')
            ->layout('layout')
            ->with([
                'user' => $member,
            ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var Member $user */
        $user = $request->user('member');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:members,email,'.$user->id],
        ]);

        if ($request->input('email') !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('members.profile')
            ->with('status', 'Profile information updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var Member $user */
        $user = $request->user('member');

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:member'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('members.profile')
            ->with('status', 'Password updated successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        /** @var Member $user */
        $user = $request->user('member');

        $request->validate([
            'password' => ['required', 'current_password:member'],
        ]);

        // Optional: Cancel Stripe subscription before deleting user
        // Add logic here if you want explicit cancellation.
        // if ($user->subscribed()) {
        //     $user->subscription('default')->cancelNow(); // Or cancel() for end of period
        // }

        Auth::guard('member')->logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been deleted.');
    }

    public function chooseYourPlan(): View
    {
        $member = Auth::guard('member')->user();
        $plans = $this->stripeService->getActivePrices();

        return (new View)
            ->template('members.choose-your-plan')
            ->layout('layout')
            ->with([
                'plans' => $plans->toArray(),
                'member' => $member,
            ]);
    }
}
