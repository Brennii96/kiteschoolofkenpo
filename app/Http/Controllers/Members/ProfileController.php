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
use Laravel\Cashier\Subscription;
use Statamic\View\View;
use Throwable;

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
            ->with($this->subscriptionViewData($member));
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

        $subscription = $member->subscription('default');

        if ($subscription && ! $subscription->ended()) {
            try {
                $subscription->cancelNow();
            } catch (Throwable $exception) {
                report($exception);

                return redirect()->route('members.profile')
                    ->withErrors([
                        'subscription' => "We couldn't cancel your subscription right now. Your account has not been deleted.",
                    ]);
            }
        }

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

        if ($member->hasIncompletePayment('default')) {
            return redirect()->route('members.profile')
                ->with('status', 'Your subscription payment is pending confirmation.');
        }

        if ($member->subscribed('default')) {
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

    /**
     * @return array{
     *     member: Member,
     *     is_subscribed: bool,
     *     has_subscription: bool,
     *     has_incomplete_payment: bool,
     *     subscription_active: bool,
     *     subscription_status: string|null,
     *     subscription_on_grace_period: bool,
     *     subscription_ends_at: mixed,
     *     payment_confirmation_url: string|null
     * }
     */
    private function subscriptionViewData(Member $member): array
    {
        $subscription = $member->subscription('default');

        return [
            'member' => $member,
            'is_subscribed' => $member->subscribed('default'),
            'has_subscription' => $subscription !== null,
            'has_incomplete_payment' => $subscription?->hasIncompletePayment() ?? false,
            'subscription_active' => $subscription?->active() ?? false,
            'subscription_status' => $subscription?->stripe_status,
            'subscription_on_grace_period' => $subscription?->onGracePeriod() ?? false,
            'subscription_ends_at' => $subscription?->ends_at,
            'payment_confirmation_url' => $this->paymentConfirmationUrl($subscription),
        ];
    }

    private function paymentConfirmationUrl(?Subscription $subscription): ?string
    {
        if (! $subscription?->hasIncompletePayment()) {
            return null;
        }

        try {
            $payment = $subscription->latestPayment();
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        if ($payment === null) {
            return null;
        }

        return route('cashier.payment', [
            $payment->id,
            'redirect' => route('members.profile'),
        ]);
    }
}
