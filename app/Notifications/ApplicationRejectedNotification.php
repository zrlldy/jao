<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationRejectedNotification extends Notification
{
    public function __construct(public string $jobTitle) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Application Update')
            ->view(
                ['mail.application-rejected', 'mail.application-rejected-text'],
                [
                    'jobTitle' => $this->jobTitle,
                ]
            );
    }
}
