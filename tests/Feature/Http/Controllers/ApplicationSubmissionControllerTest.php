<?php

use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\ApplicationInvitation;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormSection;
use App\Models\FormVersion;
use App\Models\JobHiring;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $this->formVersion = FormVersion::factory()->create(['status' => 'published']);
    $this->jobHiring->formVersions()->attach($this->formVersion);
    $this->field = FormField::factory()->for(FormSection::factory()->for($this->formVersion))
        ->create(['type' => 'text', 'is_required' => true]);
    $this->token = 'applicant-token';
    $this->invitation = ApplicationInvitation::factory()->for($this->jobHiring)->create([
        'form_version_id' => $this->formVersion->id,
        'token' => hash('sha256', $this->token),
        'expired_at' => now()->addDays(7)->toDateString(),
    ]);
});

test('an applicant submits answers without an internal API key and consumes the invitation', function () {
    $payload = [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ];

    $this->postJson(route('application-invitations.submissions.store', $this->token), $payload)
        ->assertOk();

    $application = Application::query()->sole();
    expect($application->application_invitation_id)->toBe($this->invitation->id)
        ->and($application->job_hiring_id)->toBe($this->jobHiring->id)
        ->and($application->form_version_id)->toBe($this->formVersion->id)
        ->and($this->invitation->fresh()->used_at)->not->toBeNull();

    $answer = ApplicationAnswer::query()->sole();
    expect($answer->application_id)->toBe($application->id)
        ->and($answer->form_field_id)->toBe($this->field->id)
        ->and($answer->value)->toBe('Engineer');

    $this->postJson(route('application-invitations.submissions.store', $this->token), $payload)
        ->assertNotFound();
    $this->assertDatabaseCount('applications', 1);
});

test('submission rejects a form version not published and assigned to the invitation job', function () {
    $otherVersion = FormVersion::factory()->create(['status' => 'published']);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $otherVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ])->assertUnprocessable()->assertJsonValidationErrors('form_version_id');

    $this->assertDatabaseCount('applications', 0);
    expect($this->invitation->fresh()->used_at)->toBeNull();
});

test('submission rejects another assigned published form version', function () {
    $otherVersion = FormVersion::factory()->create(['status' => 'published']);
    $this->jobHiring->formVersions()->attach($otherVersion);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $otherVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ])->assertUnprocessable()->assertJsonValidationErrors('form_version_id');

    $this->assertDatabaseCount('applications', 0);
    expect($this->invitation->fresh()->used_at)->toBeNull();
});

test('submission rejects an invitation after its pinned version is replaced', function () {
    $replacementVersion = FormVersion::factory()->create(['status' => 'published']);
    $this->jobHiring->formVersions()->detach($this->formVersion);
    $this->jobHiring->formVersions()->attach($replacementVersion);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ])->assertUnprocessable()->assertJsonValidationErrors('form_version_id');

    $this->assertDatabaseCount('applications', 0);
});

test('submission rejects an old invitation without a pinned version', function () {
    $this->invitation->update(['form_version_id' => null]);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ])->assertUnprocessable()->assertJsonValidationErrors('form_version_id');

    $this->assertDatabaseCount('applications', 0);
});

test('the database rejects a second application for one invitation', function () {
    $payload = [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ];

    $this->postJson(route('application-invitations.submissions.store', $this->token), $payload)
        ->assertOk();

    expect(fn () => Application::create([
        'application_invitation_id' => $this->invitation->id,
        'job_hiring_id' => $this->jobHiring->id,
        'form_version_id' => $this->formVersion->id,
        'status' => 'on_review',
    ]))->toThrow(QueryException::class);

    $this->assertDatabaseCount('applications', 1);
});

test('submission rejects answers to a field outside the selected form', function () {
    $otherField = FormField::factory()->create(['type' => 'text', 'is_required' => false]);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $otherField->id, 'value' => 'Other']],
    ])->assertUnprocessable()->assertJsonValidationErrors('answers.0.form_field_id');

    $this->assertDatabaseCount('applications', 0);
    expect($this->invitation->fresh()->used_at)->toBeNull();
});

test('submission requires answers for required fields', function () {
    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [],
    ])->assertUnprocessable()->assertJsonValidationErrors('answers');

    $this->assertDatabaseCount('applications', 0);
    expect($this->invitation->fresh()->used_at)->toBeNull();
});

test('submission rejects an empty value for a required field', function () {
    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => null]],
    ])->assertUnprocessable()->assertJsonValidationErrors('answers.0.value');

    $this->assertDatabaseCount('applications', 0);
});

test('submission rejects duplicate field answers', function () {
    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [
            ['form_field_id' => $this->field->id, 'value' => 'First'],
            ['form_field_id' => $this->field->id, 'value' => 'Second'],
        ],
    ])->assertUnprocessable()->assertJsonValidationErrors('answers.1.form_field_id');

    $this->assertDatabaseCount('applications', 0);
});

test('submission rejects an option value not offered by its field', function () {
    $this->field->update(['type' => 'select']);
    FormFieldOption::factory()->for($this->field)->create(['value' => 'yes']);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'no']],
    ])->assertUnprocessable()->assertJsonValidationErrors('answers.0.value');

    $this->assertDatabaseCount('applications', 0);
});

test('submission rejects an invalid date value', function () {
    $this->field->update(['type' => 'date']);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'tomorrow']],
    ])->assertUnprocessable()->assertJsonValidationErrors('answers.0.value');

    $this->assertDatabaseCount('applications', 0);
});

test('submission rejects an expired invitation without writing an application', function () {
    $this->invitation->update(['expired_at' => now()->subDay()->toDateString()]);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ])->assertNotFound();

    $this->assertDatabaseCount('applications', 0);
});

test('submission rejects an invitation after its job closes or is unpublished', function () {
    $payload = [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ];

    $this->jobHiring->update(['closed_at' => today()->subDay()]);
    $this->postJson(route('application-invitations.submissions.store', $this->token), $payload)
        ->assertNotFound();

    $this->jobHiring->update(['closed_at' => today()->addDay(), 'status' => 'draft']);
    $this->postJson(route('application-invitations.submissions.store', $this->token), $payload)
        ->assertNotFound();

    $this->assertDatabaseCount('applications', 0);
    expect($this->invitation->fresh()->used_at)->toBeNull();
});

test('submission accepts an invitation through its expiry date', function () {
    $this->travelTo(today()->setTime(15, 0));
    $this->invitation->update(['expired_at' => today()->toDateString()]);

    $this->postJson(route('application-invitations.submissions.store', $this->token), [
        'form_version_id' => $this->formVersion->id,
        'answers' => [['form_field_id' => $this->field->id, 'value' => 'Engineer']],
    ])->assertOk();

    $this->assertDatabaseCount('applications', 1);
    expect($this->invitation->fresh()->used_at)->not->toBeNull();
});
