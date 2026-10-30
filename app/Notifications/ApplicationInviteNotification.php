<?php

namespace App\Notifications;

use App\Models\ApplicationInvitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationInviteNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ApplicationInvitation $invitation,
        public string $token
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = 'https://'.rtrim(config('services.jao.url'), '/')
            .'/application/form/'
            .$this->token;

        return (new MailMessage)
            ->subject('Job Application Invitation')
            ->view(
                ['mail.application-invitation', 'mail.application-invitation-text'],
                ['applicationUrl' => $url, 'expiresOn' => $this->invitation->expired_at]
            );
    }
}
