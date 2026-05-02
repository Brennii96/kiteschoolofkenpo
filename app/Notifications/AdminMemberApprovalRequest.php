<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

final class AdminMemberApprovalRequest extends Notification
{
    use Queueable;

    public function __construct(private readonly Member $member)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $approvalUrl = URL::temporarySignedRoute(
            'member.approve',
            Carbon::now()->addDays(Config::get('auth.approval.expire', 7)),
            [
                'id' => $this->member->getKey(),
                'hash' => hash_hmac('sha256', $this->member->email, config('app.key')),
            ]
        );

        return new MailMessage()
            ->subject('New Member Registration – Approval Required')
            ->greeting('New member registration')
            ->line("**{$this->member->name}** ({$this->member->email}) has registered and is awaiting your approval.")
            ->action('Approve Member', $approvalUrl)
            ->line('This link expires in '.Config::get('auth.approval.expire', 7).' days.')
            ->line('If you did not expect this registration, you can ignore this email.');
    }
}
