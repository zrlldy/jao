<?php

use App\Http\Middleware\VerifyHmacSignature;
use App\Jobs\SendApplicantInvitation;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use App\Models\JobHiring;
use App\Notifications\ApplicationInviteNotification;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyHmacSignature::class);
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'invitation-test-key',
    ]);

    Queue::fake();
});

test('a published job with a published form can create a hashed invitation', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $formVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($formVersion);

    $this->postJson(route('job-hirings.invitations.store', $jobHiring), [
        'email' => 'applicant@example.com',
    ], ['X-API-Key' => 'invitation-test-key'])
        ->assertOk()
        ->assertExactJson(['message' => 'Invitation queued for delivery']);

    $invitation = ApplicationInvitation::query()->sole();

    expect($invitation->job_hiring_id)->toBe($jobHiring->id)
        ->and($invitation->form_version_id)->toBe($formVersion->id)
        ->and($invitation->email)->toBe('applicant@example.com')
        ->and($invitation->token)->toMatch('/^[a-f0-9]{64}$/')
        ->and($invitation->expired_at)->toBe(today()->addDays(7)->toDateString())
        ->and(is_subclass_of(SendApplicantInvitation::class, ShouldBeEncrypted::class))->toBeTrue();

    Queue::assertPushed(SendApplicantInvitation::class, function (SendApplicantInvitation $job) use ($invitation) {
        return $job->applicationInvitation->is($invitation)
            && hash('sha256', $job->token) === $invitation->token;
    });
});

test('invitation creation pins the latest assigned published form version', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $olderVersion = FormVersion::factory()->create(['status' => 'published', 'version' => 901]);
    $newerVersion = FormVersion::factory()->create(['status' => 'published', 'version' => 902]);
    $jobHiring->formVersions()->attach([$olderVersion->id, $newerVersion->id]);

    $this->postJson(route('job-hirings.invitations.store', $jobHiring), [
        'email' => 'applicant@example.com',
    ], ['X-API-Key' => 'invitation-test-key'])->assertOk();

    expect(ApplicationInvitation::query()->sole()->form_version_id)->toBe($newerVersion->id);
});

test('the encrypted invitation job sends mail without queueing the bearer token again', function () {
    Mail::fake();
    Event::fake([NotificationSent::class]);

    $invitation = new ApplicationInvitation([
        'email' => 'applicant@example.com',
        'expired_at' => today()->addDays(7)->toDateString(),
    ]);

    (new SendApplicantInvitation($invitation, 'raw-bearer-token'))->handle();

    Queue::assertNothingPushed();
    Event::assertDispatched(NotificationSent::class);
});

test('invitation mail states the final valid calendar date', function () {
    $invitation = new ApplicationInvitation(['expired_at' => '2026-11-05']);

    $message = (new ApplicationInviteNotification($invitation, 'raw-bearer-token'))->toMail($invitation);

    expect($message->outroLines)->toContain('This invitation is valid through 2026-11-05.');
});

test('the public form remains available through the invitation expiry date', function () {
    $this->travelTo(today()->setTime(15, 0));
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $formVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($formVersion);
    $token = 'expiry-day-token';
    $invitation = ApplicationInvitation::factory()->for($jobHiring)->create([
        'form_version_id' => $formVersion->id,
        'token' => hash('sha256', $token),
        'expired_at' => today()->toDateString(),
    ]);

    $this->getJson(route('application-invitations.form.show', $token))->assertOk();

    $invitation->update(['expired_at' => today()->subDay()->toDateString()]);
    $this->getJson(route('application-invitations.form.show', $token))->assertUnauthorized();
});

test('the public form rejects an invitation after its job closes or is unpublished', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $formVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($formVersion);
    $token = 'withdrawn-job-token';
    ApplicationInvitation::factory()->for($jobHiring)->create([
        'form_version_id' => $formVersion->id,
        'token' => hash('sha256', $token),
        'expired_at' => today()->addDays(7)->toDateString(),
    ]);

    $jobHiring->update(['closed_at' => today()->subDay()]);
    $this->getJson(route('application-invitations.form.show', $token))->assertUnauthorized();

    $jobHiring->update(['closed_at' => today()->addDay(), 'status' => 'draft']);
    $this->getJson(route('application-invitations.form.show', $token))->assertUnauthorized();
});

test('the public form rejects an invitation after its pinned version is replaced', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $pinnedVersion = FormVersion::factory()->create(['status' => 'published']);
    $replacementVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($pinnedVersion);
    $token = 'replaced-form-token';
    ApplicationInvitation::factory()->for($jobHiring)->create([
        'form_version_id' => $pinnedVersion->id,
        'token' => hash('sha256', $token),
    ]);

    $this->getJson(route('application-invitations.form.show', $token))
        ->assertOk()
        ->assertJsonPath('data.id', $pinnedVersion->id);

    $jobHiring->formVersions()->detach($pinnedVersion);
    $jobHiring->formVersions()->attach($replacementVersion);

    $this->getJson(route('application-invitations.form.show', $token))->assertUnauthorized();
});

test('the public form rejects an old invitation without a pinned version', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $formVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($formVersion);
    $token = 'legacy-form-token';
    ApplicationInvitation::factory()->for($jobHiring)->create([
        'form_version_id' => null,
        'token' => hash('sha256', $token),
    ]);

    $this->getJson(route('application-invitations.form.show', $token))->assertUnauthorized();
});

test('deleting an unused form version revokes its invitation without deleting the invitation', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $formVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($formVersion);
    $token = 'deleted-form-token';
    $invitation = ApplicationInvitation::factory()->for($jobHiring)->create([
        'form_version_id' => $formVersion->id,
        'token' => hash('sha256', $token),
    ]);

    $formVersion->delete();

    expect($invitation->fresh()->form_version_id)->toBeNull();
    $this->getJson(route('application-invitations.form.show', $token))->assertUnauthorized();
});

test('invitation creation rejects a draft job', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'draft']);
    $formVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($formVersion);

    $this->postJson(route('job-hirings.invitations.store', $jobHiring), [
        'email' => 'applicant@example.com',
    ], ['X-API-Key' => 'invitation-test-key'])->assertUnprocessable();

    expect(ApplicationInvitation::query()->count())->toBe(0);
    Queue::assertNothingPushed();
});

test('invitation creation rejects jobs before publication or after closing', function () {
    $formVersion = FormVersion::factory()->create(['status' => 'published']);

    foreach ([['published_at' => today()->addDay()], ['closed_at' => today()->subDay()]] as $dates) {
        $jobHiring = JobHiring::factory()->create(['status' => 'published', ...$dates]);
        $jobHiring->formVersions()->attach($formVersion);

        $this->postJson(route('job-hirings.invitations.store', $jobHiring), [
            'email' => 'applicant@example.com',
        ], ['X-API-Key' => 'invitation-test-key'])->assertUnprocessable();
    }

    expect(ApplicationInvitation::query()->count())->toBe(0);
    Queue::assertNothingPushed();
});

test('invitation creation rejects a job without an assigned published form', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);

    $this->postJson(route('job-hirings.invitations.store', $jobHiring), [
        'email' => 'applicant@example.com',
    ], ['X-API-Key' => 'invitation-test-key'])->assertUnprocessable();

    expect(ApplicationInvitation::query()->count())->toBe(0);
    Queue::assertNothingPushed();
});

test('invitation creation rejects invalid input without queueing mail', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);

    $this->postJson(route('job-hirings.invitations.store', $jobHiring), [
        'email' => str_repeat('a', 256).'@example.com',
    ], ['X-API-Key' => 'invitation-test-key'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');

    Queue::assertNothingPushed();
});

test('invitation creation requires the internal API key', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);

    $this->postJson(route('job-hirings.invitations.store', $jobHiring), [
        'email' => 'applicant@example.com',
    ])->assertUnauthorized();

    Queue::assertNothingPushed();
});
