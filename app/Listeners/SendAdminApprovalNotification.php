<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Member;
use App\Notifications\AdminMemberApprovalRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Notifications\AnonymousNotifiable;
use Throwable;

final class SendAdminApprovalNotification
{
    public function handle(Registered $event): void
    {
        if (! $event->user instanceof Member) {
            return;
        }

        $adminEmail = config('auth.admin_email');

        if (empty($adminEmail)) {
            return;
        }

        try {
            new AnonymousNotifiable()
                ->route('mail', $adminEmail)
                ->notify(new AdminMemberApprovalRequest($event->user));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
