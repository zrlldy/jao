<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InterviewInvitationNotification extends Notification
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
            ->subject('Interview Invitation')
            ->view(
                ['mail.interview-invitation', 'mail.interview-invitation-text'],
                [
                    'jobTitle' => $this->jobTitle,
                    'interviewDate' => $this->interviewDate,
                    'interviewTime' => $this->interviewTime,
                    'interviewLocation' => $this->interviewLocation,
                ]
            );
    }
}
