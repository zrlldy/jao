<?php

use App\Notifications\TestMailNotification;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;

test('the password reset notification renders its content and action in HTML', function () {
    $message = (new TestMailNotification)->toMail(new AnonymousNotifiable);

    $html = $message->render()->toHtml();

    expect($html)
        ->toContain('<title>Password Reset</title>', '<h1', 'Hi Jerold,')
        ->toContain('We received a request to reset the password for your account.')
        ->toContain('Reset Password', 'href="'.url('/').'"')
        ->toContain('If you need any assistance, please contact our support team.')
        ->toContain('copy and paste the URL below', 'background-color: #dc2626')
        ->not->toContain('<pre>', '&lt;table');
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
