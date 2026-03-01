<?php

declare(strict_types=1);

namespace App\Tags;

use App\Models\Member;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;
use Statamic\Tags\Tags;

final class MemberTag extends Tags
{
    protected static $handle = 'member';

    /**
     * The {{ member }} tag, so this returns the Member object...
     */
    public function index(): Member|Authenticatable
    {
        /** @var StatefulGuard $memberGuard */
        $memberGuard = Auth::guard('member');

        return $memberGuard->user();
    }

    /**
     * The {{ member:check }} tag.
     */
    public function check(): bool
    {
        return (bool) Auth::guard('member')->check();
    }
}
