<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OnboardingInvitationNotification extends Notification
{
    public function __construct(public string $jobTitle, public string $onboardingUrl)
    {
        if (filter_var($onboardingUrl, FILTER_VALIDATE_URL) === false || parse_url($onboardingUrl, PHP_URL_SCHEME) !== 'https') {
            throw new \InvalidArgumentException('Onboarding URL must be an HTTPS URL.');
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
            ->subject('Your Onboarding Starts Here')
            ->view(
                ['mail.onboarding-invitation', 'mail.onboarding-invitation-text'],
                [
                    'jobTitle' => $this->jobTitle,
                    'onboardingUrl' => $this->onboardingUrl,
                ]
            );
    }
}
