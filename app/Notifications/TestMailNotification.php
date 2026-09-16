<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestMailNotification extends Notification
{
    use Queueable;

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
        return (new MailMessage)
            ->subject('Password Reset')
            ->greeting('Hi Jerold,')
            ->line('We received a request to reset the password for your account.')
            ->line('Click the button below to create a new password:')
            ->action('Reset Password', url('/'))
            ->line('For your security, this password reset link will expire after 30 minutes. If you did not request a password reset, you can safely ignore this email. Your password will remain unchanged.')
            ->line('If you need any assistance, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
