<?php

use App\Models\ApplicationInvitation;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormSection;
use App\Models\FormVersion;
use App\Models\JobHiring;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('the applicant form displays sections fields and options in sort order', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'published']);
    $formVersion = FormVersion::factory()->create(['status' => 'published']);
    $jobHiring->formVersions()->attach($formVersion);

    $laterSection = FormSection::factory()->for($formVersion)->create(['sort_order' => 2]);
    $firstSection = FormSection::factory()->for($formVersion)->create(['sort_order' => 1]);
    $laterField = FormField::factory()->for($firstSection)->create(['sort_order' => 2]);
    $firstField = FormField::factory()->for($firstSection)->create(['type' => 'select', 'sort_order' => 1]);
    $laterOption = FormFieldOption::factory()->for($firstField)->create(['sort_order' => 2]);
    $firstOption = FormFieldOption::factory()->for($firstField)->create(['sort_order' => 1]);

    $token = 'ordered-form-token';
    ApplicationInvitation::factory()->for($jobHiring)->create([
        'form_version_id' => $formVersion->id,
        'token' => hash('sha256', $token),
        'expired_at' => today()->addDays(7)->toDateString(),
    ]);

    $this->getJson(route('application-invitations.form.show', $token))
        ->assertOk()
        ->assertJsonPath('data.sections.0.id', $firstSection->id)
        ->assertJsonPath('data.sections.1.id', $laterSection->id)
        ->assertJsonPath('data.sections.0.fields.0.id', $firstField->id)
        ->assertJsonPath('data.sections.0.fields.1.id', $laterField->id)
        ->assertJsonPath('data.sections.0.fields.0.options.0.id', $firstOption->id)
        ->assertJsonPath('data.sections.0.fields.0.options.1.id', $laterOption->id);
});
