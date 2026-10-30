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
    config([
        'services.jao.api_key' => 'test-jao-key',
        'services.api.header' => 'X-JAO-Key',
    ]);
});

it('returns the saved question and its section after creating a two-character label', function () {
    $section = FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft']))->create();

    $response = $this->withHeaders(['X-JAO-Key' => 'test-jao-key'])
        ->postJson("/api/form-sections/{$section->id}/form-fields", [
            'key' => 'employee_id',
            'label' => 'ID',
            'type' => 'text',
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.label', 'ID')
        ->assertJsonPath('data.form_section.id', $section->id)
        ->assertJsonPath('data.form_section.title', $section->title);
    $this->assertDatabaseHas('form_fields', [
        'id' => $response->json('data.id'),
        'form_section_id' => $section->id,
        'key' => 'employee_id',
        'label' => 'ID',
    ]);
    $this->assertDatabaseCount('form_fields', 1);
});

it('returns 422 without creating a question when the label has one character', function () {
    $section = FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft']))->create();

    $response = $this->withHeaders(['X-JAO-Key' => 'test-jao-key'])
        ->postJson("/api/form-sections/{$section->id}/form-fields", [
            'key' => 'employee_id',
            'label' => 'I',
            'type' => 'text',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('label')
        ->assertJsonPath('errors.label.0', 'The label field must be at least 2 characters.');
    $this->assertDatabaseCount('form_fields', 0);
});

it('rejects unsupported question types', function () {
    $section = FormSection::factory()->create();

    $this->withHeaders(['X-JAO-Key' => 'test-jao-key'])
        ->postJson(route('form-sections.form-fields.store', $section), [
            'key' => 'document',
            'label' => 'Document',
            'type' => 'javascript',
        ])->assertUnprocessable()->assertJsonValidationErrors('type');

    $this->assertDatabaseCount('form_fields', 0);
});

it('does not edit a question in a published version', function () {
    $version = FormVersion::factory()->create(['status' => 'published']);
    $field = FormField::factory()->for(FormSection::factory()->for($version))->create();

    $this->withHeaders(['X-JAO-Key' => 'test-jao-key'])
        ->patchJson(route('form-fields.update', $field), ['label' => 'Changed'])
        ->assertStatus(409);

    expect($field->fresh()->label)->not->toBe('Changed');
});

it('does not move a question into another form version', function () {
    $source = FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft']))->create();
    $destination = FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft']))->create();
    $field = FormField::factory()->for($source)->create(['sort_order' => 1]);

    $this->withHeaders(['X-JAO-Key' => 'test-jao-key'])
        ->patchJson(route('form-fields.position.update', $field), [
            'form_section_id' => $destination->id,
            'sort_order' => 1,
        ])->assertUnprocessable()->assertJsonValidationErrors('form_section_id');

    expect($field->fresh()->form_section_id)->toBe($source->id);
});

it('moves a question between sections of the same draft version', function () {
    $version = FormVersion::factory()->create(['status' => 'draft']);
    $source = FormSection::factory()->for($version)->create();
    $destination = FormSection::factory()->for($version)->create();
    $moved = FormField::factory()->for($source)->create(['sort_order' => 1]);
    $existing = FormField::factory()->for($destination)->create(['sort_order' => 1]);

    $this->withHeaders(['X-JAO-Key' => 'test-jao-key'])
        ->patchJson(route('form-fields.position.update', $moved), [
            'form_section_id' => $destination->id,
            'sort_order' => 1,
        ])->assertOk();

    expect($moved->fresh()->form_section_id)->toBe($destination->id)
        ->and($moved->fresh()->sort_order)->toBe(1)
        ->and($existing->fresh()->sort_order)->toBe(2);
});

it('retains a question with a saved answer', function () {
    $version = FormVersion::factory()->create(['status' => 'draft']);
    $field = FormField::factory()->for(FormSection::factory()->for($version))->create();
    $answer = ApplicationAnswerFactory::new()->for($field)->create();

    $this->withHeaders(['X-JAO-Key' => 'test-jao-key'])
        ->deleteJson(route('form-fields.destroy', $field))->assertStatus(409);

    $this->assertModelExists($field);
    $this->assertModelExists($answer);
});
