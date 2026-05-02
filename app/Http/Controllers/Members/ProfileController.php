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
    ) {
    }

    public function show(): View
    {
        /** @var \App\Models\Member $member */
        $member = Auth::guard('member')->user();

        return new View()
            ->template('members.profile')
            ->layout('layout')
            ->with([
                'member' => $member,
                'is_subscribed' => $member->subscribed('default'),
            ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:members,email,'.$member->id],
        ]);

        if ($request->input('email') !== $member->email) {
            $member->email_verified_at = null;
        }

        $member->fill($validated);
        $member->save();

        return redirect()->route('members.profile')
            ->with('status', 'Profile information updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password:member'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $member->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('members.profile')
            ->with('status', 'Password updated successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        $request->validate([
            'password' => ['required', 'current_password:member'],
        ]);

        Auth::guard('member')->logout();

        $member->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been deleted.');
    }

    public function pendingApproval(Request $request): View|RedirectResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        if ($member->isApproved()) {
            return redirect()->route('members.choose-your-plan')
                ->with('status', 'Your account has been approved! Choose a plan to get started.');
        }

        return (new View())
            ->template('members.pending-approval')
            ->layout('layout');
    }

    public function chooseYourPlan(): View|RedirectResponse
    {
        /** @var \App\Models\Member $member */
        $member = Auth::guard('member')->user();

        if ($member->subscribed()) {
            return $member->redirectToBillingPortal(route('members.profile'));
        }

        $plans = $this->stripeService->getActivePrices();

        return (new View())
            ->template('members.choose-your-plan')
            ->layout('layout')
            ->with([
                'plans' => $plans->toArray(),
                'member' => $member,
                'stripe_key' => config('cashier.key'),
            ]);
    }
}
