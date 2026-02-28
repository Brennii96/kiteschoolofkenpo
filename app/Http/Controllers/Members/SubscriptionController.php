<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Exception\InvalidRequestException;

class SubscriptionController extends Controller
{
    /**
     * @param Request $request
     * @param Member $member
     * @return JsonResponse
     */
    public function subscribe(Request $request, Member $member): JsonResponse
    {
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
            return response()->json([
                'message' => 'Invalid Request. Check price id.',
            ], 400);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Subscription failed.'], 500);
        }
    }

    /**
     * @param Member $member
     * @return JsonResponse
     */
    public function cancel(Member $member): JsonResponse
    {
        if ($member->subscribed('default')) {
            $member->subscription('default')->cancel();
            return response()->json(['message' => 'Subscription cancelled.']);
        }

        return response()->json(['message' => 'No active subscription.']);
    }

    /**
     * @param Member $member
     * @return JsonResponse
     */
    public function resume(Member $member): JsonResponse
    {
        if ($member->subscription('default') && $member->subscription('default')->onGracePeriod()) {
            $member->subscription('default')->resume();
            return response()->json(['message' => 'Subscription resumed.']);
        }

        return response()->json(['message' => 'Cannot resume subscription.']);
    }

    /**
     * @param Member $member
     * @return JsonResponse
     */
    public function subscriptionStatus(Member $member): JsonResponse
    {
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

    /**
     * @param Request $request
     * @param Member $member
     * @return JsonResponse
     */
    public function updatePaymentMethod(Request $request, Member $member): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $member->updateDefaultPaymentMethod($request->payment_method);

        return response()->json(['message' => 'Payment method updated.']);
    }

}
