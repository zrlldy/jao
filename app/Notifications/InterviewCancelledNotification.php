<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewCancelledNotification extends Notification
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
            ->subject('Interview Cancelled')
            ->view(
                ['mail.interview-cancelled', 'mail.interview-cancelled-text'],
                [
                    'jobTitle' => $this->jobTitle,
                ]
            );
    }
}
