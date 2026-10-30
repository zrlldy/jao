<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobOfferNotification extends Notification
{
    public function __construct(public string $jobTitle, public string $offerUrl)
    {
        if (filter_var($offerUrl, FILTER_VALIDATE_URL) === false || parse_url($offerUrl, PHP_URL_SCHEME) !== 'https') {
            throw new \InvalidArgumentException('Offer URL must be an HTTPS URL.');
        }
    }

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
            ->subject('Job Offer')
            ->view(
                ['mail.job-offer', 'mail.job-offer-text'],
                [
                    'jobTitle' => $this->jobTitle,
                    'offerUrl' => $this->offerUrl,
                ]
            );
    }
}
