<?php

use App\Http\Middleware\VerifyHmacSignature;
use App\Models\ApplicationInvitation;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\FormTemplate;
use App\Models\FormVersion;
use App\Models\JobHiring;
use Database\Factories\ApplicationAnswerFactory;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyHmacSignature::class);
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'form-version-test-key',
    ]);

    $this->withHeaders(['X-API-Key' => 'form-version-test-key']);
});

test('creates a draft form version without a publication date and returns it', function () {
    $formTemplate = FormTemplate::factory()->create();

    $response = $this->postJson(route('form-templates.form-versions.store', $formTemplate), [
        'status' => 'draft',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.status', 'draft')
        ->assertJsonPath('data.version', 1);

    $this->assertDatabaseHas('form_versions', [
        'id' => $response->json('data.id'),
        'form_template_id' => $formTemplate->id,
        'published_at' => null,
    ]);
});

test('publishes a populated draft and dates it', function () {
    $formVersion = FormVersion::factory()->create(['status' => 'draft', 'published_at' => null]);
    FormField::factory()->for(FormSection::factory()->for($formVersion))->create(['type' => 'text']);
    $this->travelTo('2026-10-28 10:00:00');

    $response = $this->patchJson(route('form-versions.update', $formVersion), [
        'status' => 'published',
    ]);

    $response->assertOk()->assertJsonPath('data.status', 'published');
    expect($formVersion->fresh()->published_at->toDateString())
        ->toBe('2026-10-28');
});

test('rejects direct publication or unknown status when creating a version', function (string $status) {
    $formTemplate = FormTemplate::factory()->create();

    $this->postJson(route('form-templates.form-versions.store', $formTemplate), [
        'status' => $status,
    ])->assertUnprocessable()->assertJsonValidationErrors('status');

    $this->assertDatabaseCount('form_versions', 0);
})->with(['published', 'archived', 'other']);

test('does not publish a draft without a question', function () {
    $formVersion = FormVersion::factory()->create(['status' => 'draft', 'published_at' => null]);

    $this->patchJson(route('form-versions.update', $formVersion), [
        'status' => 'published',
    ])->assertStatus(409);

    expect($formVersion->fresh()->status)->toBe('draft');
});

test('does not publish a choice question without options', function () {
    $formVersion = FormVersion::factory()->create(['status' => 'draft', 'published_at' => null]);
    FormField::factory()->for(FormSection::factory()->for($formVersion))->create(['type' => 'select']);

    $this->patchJson(route('form-versions.update', $formVersion), [
        'status' => 'published',
    ])->assertStatus(409);

    expect($formVersion->fresh()->status)->toBe('draft');
});

test('does not accept an unknown status or client publication date', function () {
    $formVersion = FormVersion::factory()->create(['status' => 'draft', 'published_at' => null]);

    $this->patchJson(route('form-versions.update', $formVersion), [
        'status' => 'other',
        'published_at' => '2026-10-28',
    ])->assertUnprocessable()->assertJsonValidationErrors(['status', 'published_at']);

    expect($formVersion->fresh()->status)->toBe('draft');
});

test('numbers versions within each template', function () {
    $firstTemplate = FormTemplate::factory()->create();
    $secondTemplate = FormTemplate::factory()->create();

    $first = $this->postJson(route('form-templates.form-versions.store', $firstTemplate), ['status' => 'draft']);
    $second = $this->postJson(route('form-templates.form-versions.store', $secondTemplate), ['status' => 'draft']);
    $next = $this->postJson(route('form-templates.form-versions.store', $firstTemplate), ['status' => 'draft']);

    $first->assertCreated()->assertJsonPath('data.version', 1);
    $second->assertCreated()->assertJsonPath('data.version', 1);
    $next->assertCreated()->assertJsonPath('data.version', 2);

    $migration = require base_path('database/migrations/2026_10_30_000001_scope_form_version_number_uniqueness.php');
    expect(fn () => $migration->down())->toThrow(RuntimeException::class);

    expect(fn () => FormVersion::factory()->for($firstTemplate)->create(['version' => 1]))
        ->toThrow(QueryException::class);
});

test('returns 409 and retains applications and answers when deleting a form version', function (?string $submittedAt) {
    $formVersion = FormVersion::factory()->create();
    $jobHiring = JobHiring::factory()->create();
    $invitation = ApplicationInvitation::factory()->for($jobHiring)->create();
    $application = ApplicationFactory::new()->for($invitation, 'applicationInvitation')
        ->for($jobHiring)->for($formVersion)->create(['submitted_at' => $submittedAt]);
    $formField = FormField::factory()->for(FormSection::factory()->for($formVersion))->create();
    $answer = ApplicationAnswerFactory::new()->for($application)->for($formField)->create();

    $this->deleteJson(route('form-versions.destroy', $formVersion))
        ->assertStatus(409)
        ->assertJsonPath('message', 'Cannot delete a form version with applications.');

    $this->assertModelExists($formVersion);
    $this->assertModelExists($application);
    $this->assertModelExists($answer);
})->with([
    'submitted application' => ['2026-10-28 10:00:00'],
    'saved draft' => null,
]);

test('deletes a form version without applications', function () {
    $formVersion = FormVersion::factory()->create();

    $this->deleteJson(route('form-versions.destroy', $formVersion))->assertNoContent();

    $this->assertModelMissing($formVersion);
});

test('retains a version when a descendant question has a legacy answer', function () {
    $formVersion = FormVersion::factory()->create(['status' => 'draft']);
    $field = FormField::factory()->for(FormSection::factory()->for($formVersion))->create();
    $answer = ApplicationAnswerFactory::new()->for($field)->create();

    $this->deleteJson(route('form-versions.destroy', $formVersion))->assertStatus(409);

    $this->assertModelExists($formVersion);
    $this->assertModelExists($answer);
});

test('retains a version assigned to a job', function () {
    $formVersion = FormVersion::factory()->create();
    $jobHiring = JobHiring::factory()->create();
    $jobHiring->formVersions()->attach($formVersion);

    $this->deleteJson(route('form-versions.destroy', $formVersion))->assertStatus(409);

    $this->assertModelExists($formVersion);
});

test('retains a version pinned by an invitation', function () {
    $formVersion = FormVersion::factory()->create();
    $jobHiring = JobHiring::factory()->create();
    ApplicationInvitation::factory()->for($jobHiring)->create(['form_version_id' => $formVersion->id]);

    $this->deleteJson(route('form-versions.destroy', $formVersion))->assertStatus(409);

    $this->assertModelExists($formVersion);
});
