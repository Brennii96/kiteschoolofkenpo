<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Member;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * @return Member|Authenticatable|null
     */
    public static function member(): Member|Authenticatable|null
    {
        return Auth::guard('member')->user();
    }
}
