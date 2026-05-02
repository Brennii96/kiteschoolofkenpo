<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Notifications\MemberApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final class ApproveMemberController extends Controller
{
    public function __invoke(Request $request, int $id): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $member = Member::findOrFail($id);

        'app.key'
            |> config(...)
            |> (fn ($x) => hash_hmac('sha256', $member->email, $x))
            |> (fn ($x) => hash_equals($x, (string)$request->route('hash')))
            |> (fn ($x) => abort_unless($x, 403));

        if ($member->approved_at !== null) {
            return redirect('/')->with('status', 'Member is already approved.');
        }

        $member->update(['approved_at' => Carbon::now()]);
        $member->notify(new MemberApprovedNotification());

        return redirect('/')->with('status', "Member {$member->name} has been approved.");
    }
}
