<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewRescheduledNotification extends Notification
{
    public function __construct(public string $jobTitle, public string $interviewDate, public string $interviewTime, public string $interviewLocation) {}

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
            ->subject('Interview Rescheduled')
            ->view(
                ['mail.interview-rescheduled', 'mail.interview-rescheduled-text'],
                [
                    'jobTitle' => $this->jobTitle,
                    'interviewDate' => $this->interviewDate,
                    'interviewTime' => $this->interviewTime,
                    'interviewLocation' => $this->interviewLocation,
                ]
            );
    }
}
