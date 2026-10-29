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

test('creates an unpublished form version without a publication date and returns it', function (string $status) {
    $formTemplate = FormTemplate::factory()->create();

    $response = $this->postJson(route('form-templates.form-versions.store', $formTemplate), [
        'status' => $status,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.status', $status)
        ->assertJsonPath('data.version', 1);

    $this->assertDatabaseHas('form_versions', [
        'id' => $response->json('data.id'),
        'form_template_id' => $formTemplate->id,
        'published_at' => null,
    ]);
})->with(['draft', 'archived']);

test('dates a published form version and returns it', function () {
    $formTemplate = FormTemplate::factory()->create();
    $this->travelTo('2026-10-28 10:00:00');

    $response = $this->postJson(route('form-templates.form-versions.store', $formTemplate), [
        'status' => 'published',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.status', 'published')
        ->assertJsonPath('data.version', 1);

    $this->assertDatabaseHas('form_versions', [
        'id' => $response->json('data.id'),
        'form_template_id' => $formTemplate->id,
    ]);

    expect(FormVersion::findOrFail($response->json('data.id'))->published_at->toDateString())
        ->toBe('2026-10-28');
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
