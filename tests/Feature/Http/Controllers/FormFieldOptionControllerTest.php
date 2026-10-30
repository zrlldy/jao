<?php

use App\Http\Middleware\VerifyHmacSignature;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormSection;
use App\Models\FormVersion;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyHmacSignature::class);
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'form-option-test-key',
    ]);
    $this->withHeaders(['X-API-Key' => 'form-option-test-key']);
});

test('does not add an option to a published question', function () {
    $field = FormField::factory()
        ->for(FormSection::factory()->for(FormVersion::factory()->state(['status' => 'published'])))
        ->create(['type' => 'select']);

    $this->postJson(route('form-fields.options.store', $field), [
        'value' => 'yes-value',
        'label' => 'Yes',
    ])->assertStatus(409);

    $this->assertDatabaseCount('form_field_options', 0);
});

test('rejects an option position beyond the final option', function () {
    $field = FormField::factory()
        ->for(FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft'])))
        ->create(['type' => 'select']);
    $option = FormFieldOption::factory()->for($field)->create(['sort_order' => 1]);

    $this->patchJson(route('form-field-options.position.update', $option), [
        'sort_order' => 2,
    ])->assertUnprocessable()->assertJsonValidationErrors('sort_order');

    expect($option->fresh()->sort_order)->toBe(1);
});

test('reorders options in a draft choice question', function () {
    $field = FormField::factory()
        ->for(FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft'])))
        ->create(['type' => 'select']);
    $first = FormFieldOption::factory()->for($field)->create(['sort_order' => 1]);
    $second = FormFieldOption::factory()->for($field)->create(['sort_order' => 2]);

    $this->patchJson(route('form-field-options.position.update', $second), [
        'sort_order' => 1,
    ])->assertOk();

    expect($first->fresh()->sort_order)->toBe(2)
        ->and($second->fresh()->sort_order)->toBe(1);
});

test('updates only the value of a draft option', function () {
    $field = FormField::factory()
        ->for(FormSection::factory()->for(FormVersion::factory()->state(['status' => 'draft'])))
        ->create(['type' => 'select']);
    $option = FormFieldOption::factory()->for($field)->create(['label' => 'Yes']);

    $this->patchJson(route('form-field-options.update', $option), [
        'value' => 'Y',
    ])->assertOk();

    expect($option->fresh()->value)->toBe('Y')
        ->and($option->fresh()->label)->toBe('Yes');
});
