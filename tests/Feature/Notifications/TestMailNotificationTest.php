<?php

use App\Models\ApplicationInvitation;
use App\Notifications\ApplicationInviteNotification;
use App\Notifications\TestMailNotification;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

test('the password reset notification renders its content and action in HTML', function () {
    $message = (new TestMailNotification)->toMail(new AnonymousNotifiable);

    $html = $message->render()->toHtml();

    expect($html)
        ->toContain('<title>Password Reset</title>', '<h1', 'Hi Jerold,')
        ->toContain('We received a request to reset the password for your account.')
        ->toContain('Reset Password', 'href="'.url('/').'"')
        ->toContain('If you need any assistance, please contact our support team.')
        ->toContain('copy and paste the URL below', 'background-color: #CB0101')
        ->not->toContain('<pre>', '&lt;table', 'application-mail-placeholder.jpg');
});

test('the password reset notification renders its content and action in plain text', function () {
    $message = (new TestMailNotification)->toMail(new AnonymousNotifiable);

    $text = app(Markdown::class)->renderText($message->markdown, $message->data())->toHtml();

    expect($text)
        ->toContain('Hi Jerold,', 'We received a request to reset the password for your account.')
        ->toContain('Reset Password: '.url('/'))
        ->toContain('If you need any assistance, please contact our support team.')
        ->toContain('copy and paste the URL below')
        ->not->toContain('<table', '<td', '<a ');
});

test('notifications without a subject or action use the app name and render the body', function () {
    config(['app.name' => 'Example Hiring']);
    $message = (new MailMessage)->line('Your application has been received.');

    $html = $message->render()->toHtml();

    expect($html)
        ->toContain('<title>Example Hiring</title>', 'Your application has been received.')
        ->not->toContain('copy and paste the URL below');
});

test('application invitation uses dedicated HTML and text views with the career link', function () {
    config(['services.jao.url' => 'career.eastequatorexpress.com', 'mail.default' => 'array']);

    $invitation = new ApplicationInvitation(['expired_at' => '2026-11-05']);
    $message = (new ApplicationInviteNotification($invitation, 'sample-token'))->toMail($invitation);
    $url = 'https://career.eastequatorexpress.com/application/form/sample-token';

    expect($message->view)->toBe(['mail.application-invitation', 'mail.application-invitation-text']);

    Notification::route('mail', 'applicant@example.test')->notify(new ApplicationInviteNotification($invitation, 'sample-token'));

    $email = Mail::mailer('array')->getSymfonyTransport()->messages()->first()->getOriginalMessage();
    $html = $email->getHtmlBody();
    $text = $email->getTextBody();

    expect($html)->toContain('<title>Job Application Invitation</title>', 'href="'.$url.'"', 'This invitation is valid through 2026-11-05.')
        ->and(substr_count($html, $url))->toBe(3)
        ->and($text)->toContain('Open Application Form: '.$url, 'This invitation is valid through 2026-11-05.')
        ->and($email->getAttachments())->toHaveCount(2)
        ->and(substr_count($html, 'cid:'))->toBe(2);
});
