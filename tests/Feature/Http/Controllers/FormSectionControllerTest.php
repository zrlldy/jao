<?php

use App\Http\Middleware\VerifyHmacSignature;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\FormVersion;
use Database\Factories\ApplicationAnswerFactory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyHmacSignature::class);
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'form-section-test-key',
    ]);
    $this->withHeaders(['X-API-Key' => 'form-section-test-key']);
});

test('does not add a section to a published form', function () {
    $version = FormVersion::factory()->create(['status' => 'published']);

    $this->postJson(route('form-versions.form-sections.store', $version), [
        'title' => 'Experience',
        'description' => 'Your work experience',
    ])->assertStatus(409);

    $this->assertDatabaseCount('form_sections', 0);
});

test('retains a section containing a saved answer', function () {
    $section = FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft']))->create();
    $field = FormField::factory()->for($section)->create();
    $answer = ApplicationAnswerFactory::new()->for($field)->create();

    $this->deleteJson(route('form-sections.destroy', $section))->assertStatus(409);

    $this->assertModelExists($section);
    $this->assertModelExists($answer);
});

test('rejects a section position beyond the final section', function () {
    $section = FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft']))
        ->create(['sort_order' => 1]);

    $this->patchJson(route('form-sections.position.update', $section), [
        'sort_order' => 2,
    ])->assertUnprocessable()->assertJsonValidationErrors('sort_order');

    expect($section->fresh()->sort_order)->toBe(1);
});

test('reorders sections within a draft form', function () {
    $version = FormVersion::factory()->create(['status' => 'draft']);
    $first = FormSection::factory()->for($version)->create(['sort_order' => 1]);
    $second = FormSection::factory()->for($version)->create(['sort_order' => 2]);

    $this->patchJson(route('form-sections.position.update', $second), [
        'sort_order' => 1,
    ])->assertOk();

    expect($first->fresh()->sort_order)->toBe(2)
        ->and($second->fresh()->sort_order)->toBe(1);
});
