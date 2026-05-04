<?php

declare(strict_types=1);

namespace App\Tags;

use App\Models\Member;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Statamic\Tags\Tags;

final class MemberTag extends Tags
{
    protected static $handle = 'member_access';

    /**
     * The {{ member_access:check }} tag.
     */
    public function check(): bool
    {
        return Auth::guard('member')->check();
    }

    /**
     * The {{ member_access:can_view subscription_required="true" }} tag.
     */
    public function can_view(): bool
    {
        return $this->canView();
    }

    /**
     * The {{ member_access:canView subscription_required="true" }} tag.
     */
    public function canView(): bool
    {
        $subscriptionRequired = $this->params->bool('subscription_required', true);

        if (! $subscriptionRequired) {
            return true;
        }

        $member = $this->member();

        return $member instanceof Member
            && $member->hasVerifiedEmail()
            && $member->isApproved()
            && $member->subscribed();
    }

    private function member(): Member|Authenticatable|null
    {
        return Auth::guard('member')->user();
    }
}
