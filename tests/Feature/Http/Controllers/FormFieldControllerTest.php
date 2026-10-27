<?php

use App\Models\FormSection;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    config([
        'services.jao.api_key' => 'test-jao-key',
        'services.api.header' => 'X-JAO-Key',
    ]);
});

it('returns the saved question and its section after creating a two-character label', function () {
    $section = FormSection::factory()->create();

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
    $section = FormSection::factory()->create();

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
