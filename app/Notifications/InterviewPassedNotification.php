<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewPassedNotification extends Notification
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
            ->subject('Interview Update')
            ->view(
                ['mail.interview-passed', 'mail.interview-passed-text'],
                [
                    'jobTitle' => $this->jobTitle,
                ]
            );
    }
}
