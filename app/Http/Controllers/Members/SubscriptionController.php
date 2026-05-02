<?php

declare(strict_types=1);

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\InvalidRequestException;

final class SubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        $request->validate([
            'payment_method' => 'required|string',
            'price_id' => 'required|string',
        ]);

        try {
            $member->newSubscription('default', $request->price_id)
                ->create($request->payment_method);

            return response()->json(['message' => 'Subscription created successfully.']);
        } catch (IncompletePayment $exception) {
            return response()->json([
                'message' => 'Payment requires further action.',
                'payment' => $exception->payment->client_secret,
            ], 426);
        } catch (InvalidRequestException $exception) {
            return response()->json(['message' => 'Invalid Request. Check price id.'], 400);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Subscription failed.'], 500);
        }
    }

    public function cancel(Request $request): JsonResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        if ($member->subscribed('default')) {
            $member->subscription('default')->cancel();

            return response()->json(['message' => 'Subscription cancelled.']);
        }

        return response()->json(['message' => 'No active subscription.']);
    }

    public function resume(Request $request): JsonResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        if ($member->subscription('default')?->onGracePeriod()) {
            $member->subscription('default')->resume();

            return response()->json(['message' => 'Subscription resumed.']);
        }

        return response()->json(['message' => 'Cannot resume subscription.']);
    }

    public function subscriptionStatus(Request $request): JsonResponse
    {
        /** @var Member $member */
        $member = $request->user('member');
        $subscription = $member->subscription('default');

        if ($subscription) {
            return response()->json([
                'subscribed' => $member->subscribed('default'),
                'active' => $subscription->active(),
                'on_grace_period' => $subscription->onGracePeriod(),
                'ends_at' => $subscription->ends_at,
                'price' => $subscription->stripe_price,
            ]);
        }

        return response()->json(['message' => 'No active subscription.']);
    }

    public function billingPortal(Request $request): RedirectResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        return $member->redirectToBillingPortal(route('members.profile'));
    }

    public function updatePaymentMethod(Request $request): JsonResponse
    {
        /** @var Member $member */
        $member = $request->user('member');

        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $member->updateDefaultPaymentMethod($request->payment_method);

        return response()->json(['message' => 'Payment method updated.']);
    }
}
