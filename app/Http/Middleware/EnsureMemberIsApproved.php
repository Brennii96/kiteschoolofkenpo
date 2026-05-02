<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $member = Auth::guard('member')->user();

        if ($member === null || $member->approved_at !== null) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Your account is pending admin approval.'], 403);
        }

        return redirect()->route('members.pending-approval');
    }
}
