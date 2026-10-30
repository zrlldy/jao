<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceivedNotification extends Notification
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
            ->subject('Application Received')
            ->view(
                ['mail.application-received', 'mail.application-received-text'],
                [
                    'jobTitle' => $this->jobTitle,
                ]
            );
    }
}
