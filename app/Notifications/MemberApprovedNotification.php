<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class MemberApprovedNotification extends Notification
{
    use Queueable;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return new MailMessage()
            ->subject('Your membership has been approved!')
            ->greeting("Welcome, $notifiable->name!")
            ->line('Your account has been approved. You can now choose a membership plan.')
            ->action('Choose a Plan', route('members.choose-your-plan'))
            ->line('We look forward to seeing you on the mat.');
    }
}
