<?php

use App\Notifications\ApplicationReceivedNotification;
use App\Notifications\ApplicationRejectedNotification;
use App\Notifications\InterviewCancelledNotification;
use App\Notifications\InterviewInvitationNotification;
use App\Notifications\InterviewPassedNotification;
use App\Notifications\InterviewRescheduledNotification;
use App\Notifications\JobOfferNotification;
use App\Notifications\OnboardingInvitationNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

test('each hiring stage sends its own HTML and plain-text email', function (
    string $notificationClass,
    array $arguments,
    string $subject,
    string $viewName,
    string $expectedCopy,
    bool $hasInterviewDetails,
    ?string $actionUrl
) {
    config(['mail.default' => 'array']);

    $notification = new $notificationClass(...$arguments);
    $message = $notification->toMail(new AnonymousNotifiable);

    expect($message->view)->toBe(['mail.'.$viewName, 'mail.'.$viewName.'-text']);

    Notification::route('mail', 'applicant@example.test')->notify($notification);

    $email = Mail::mailer('array')->getSymfonyTransport()->messages()->first()->getOriginalMessage();
    $html = $email->getHtmlBody();
    $text = $email->getTextBody();

    expect($email->getSubject())->toBe($subject);
    expect($html)->toContain('<title>'.$subject.'</title>', $expectedCopy, 'Fleet &amp; Operations Lead', 'font-family:Poppins', 'background-color:#FDFDFF')
        ->not->toContain('application-mail-placeholder.jpg');
    expect($text)->toContain($expectedCopy, 'Fleet & Operations Lead')
        ->not->toContain('<table', '<img');
    expect($email->getAttachments())->toHaveCount(1);
    expect(substr_count($html, 'cid:'))->toBe(1);

    if ($hasInterviewDetails) {
        expect($html)->toContain('Nov 12, 2026', '9:30 AM', 'Cebu City office');
        expect($text)->toContain('Date: Nov 12, 2026', 'Time: 9:30 AM', 'Location: Cebu City office');
    }

    if ($actionUrl !== null) {
        expect(substr_count($html, $actionUrl))->toBe(3);
        expect($text)->toContain($actionUrl);
    }
})->with([
    'application received' => [ApplicationReceivedNotification::class, ['Fleet & Operations Lead'], 'Application Received', 'application-received', 'Thank you for applying', false, null],
    'interview invitation' => [InterviewInvitationNotification::class, ['Fleet & Operations Lead', 'Nov 12, 2026', '9:30 AM', 'Cebu City office'], 'Interview Invitation', 'interview-invitation', 'We would like to invite you', true, null],
    'interview rescheduled' => [InterviewRescheduledNotification::class, ['Fleet & Operations Lead', 'Nov 12, 2026', '9:30 AM', 'Cebu City office'], 'Interview Rescheduled', 'interview-rescheduled', 'has been rescheduled', true, null],
    'interview cancelled' => [InterviewCancelledNotification::class, ['Fleet & Operations Lead'], 'Interview Cancelled', 'interview-cancelled', 'will no longer take place', false, null],
    'interview passed' => [InterviewPassedNotification::class, ['Fleet & Operations Lead'], 'Interview Update', 'interview-passed', 'You passed the interview stage', false, null],
    'application rejected' => [ApplicationRejectedNotification::class, ['Fleet & Operations Lead'], 'Application Update', 'application-rejected', 'We will not be moving forward', false, null],
    'job offer' => [JobOfferNotification::class, ['Fleet & Operations Lead', 'https://career.example.test/offer/example'], 'Job Offer', 'job-offer', 'Please review the full offer', false, 'https://career.example.test/offer/example'],
    'onboarding invitation' => [OnboardingInvitationNotification::class, ['Fleet & Operations Lead', 'https://career.example.test/onboarding/example'], 'Your Onboarding Starts Here', 'onboarding-invitation', 'Open your onboarding checklist', false, 'https://career.example.test/onboarding/example'],
]);

test('offer and onboarding actions reject non-HTTPS URLs', function (string $notificationClass) {
    new $notificationClass('Fleet Operations Lead', 'http://example.test/action');
})->with([
    JobOfferNotification::class,
    OnboardingInvitationNotification::class,
])->throws(InvalidArgumentException::class);
