<?php

use App\Models\Department;
use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormSection;
use Database\Factories\ApplicationAnswerFactory;
use Database\Factories\ApplicationFactory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'routing-test-key',
    ]);

    $this->withHeaders(['X-API-Key' => 'routing-test-key']);
});

test('returns the answer belonging to the requested application', function () {
    $application = ApplicationFactory::new()->create();
    $answer = ApplicationAnswerFactory::new()->for($application)->create(['value' => 'Expected answer']);

    $this->getJson(route('applications.application-answers.show', [$application, $answer]))
        ->assertOk()
        ->assertJsonPath('data.id', $answer->id)
        ->assertJsonPath('data.applicant.application_id', $application->id)
        ->assertJsonPath('data.value', 'Expected answer');
});

test('returns 404 for an answer belonging to another application', function () {
    $application = ApplicationFactory::new()->create();
    $answer = ApplicationAnswerFactory::new()->create();

    $this->getJson(route('applications.application-answers.show', [$application, $answer]))
        ->assertNotFound();
});

test('generates named URLs for API resources and custom endpoints', function (string $name, array $parameters, string $path) {
    expect(route($name, $parameters, false))->toBe($path);
})->with([
    'departments' => ['departments.index', [], '/api/departments'],
    'employment types' => ['employment-types.show', ['type-id'], '/api/employment-types/type-id'],
    'locations' => ['locations.index', [], '/api/locations'],
    'job hirings' => ['job-hirings.show', ['job-id'], '/api/job-hirings/job-id'],
    'form templates' => ['form-templates.index', [], '/api/form-templates'],
    'nested form versions' => ['form-templates.form-versions.store', ['template-id'], '/api/form-templates/template-id/form-versions'],
    'shallow form versions' => ['form-versions.show', ['version-id'], '/api/form-versions/version-id'],
    'nested form sections' => ['form-versions.form-sections.index', ['version-id'], '/api/form-versions/version-id/form-sections'],
    'shallow form sections' => ['form-sections.show', ['section-id'], '/api/form-sections/section-id'],
    'nested form fields' => ['form-sections.form-fields.store', ['section-id'], '/api/form-sections/section-id/form-fields'],
    'shallow form fields' => ['form-fields.show', ['field-id'], '/api/form-fields/field-id'],
    'nested options' => ['form-fields.options.store', ['field-id'], '/api/form-fields/field-id/options'],
    'shallow options' => ['form-field-options.show', ['option-id'], '/api/form-field-options/option-id'],
    'job form assignments' => ['job-hirings.form-versions.destroy', ['job-id', 'version-id'], '/api/job-hirings/job-id/form-versions/version-id'],
    'invitation creation' => ['job-hirings.invitations.store', ['job-id'], '/api/job-hirings/job-id/invitations'],
    'invitation form' => ['application-invitations.form.show', ['invitation-token'], '/api/application-invitations/invitation-token/form'],
    'invitation submissions' => ['application-invitations.submissions.store', ['invitation-token'], '/api/application-invitations/invitation-token/submissions'],
    'applications' => ['applications.index', [], '/api/applications'],
    'application detail' => ['applications.show', ['application-id'], '/api/applications/application-id'],
    'application answers' => ['applications.application-answers.index', ['application-id'], '/api/applications/application-id/application-answers'],
    'answer detail' => ['applications.application-answers.show', ['application-id', 'answer-id'], '/api/applications/application-id/application-answers/answer-id'],
    'section position' => ['form-sections.position.update', ['section-id'], '/api/form-sections/section-id/position'],
    'field position' => ['form-fields.position.update', ['field-id'], '/api/form-fields/field-id/position'],
    'option position' => ['form-field-options.position.update', ['option-id'], '/api/form-field-options/option-id/position'],
]);

test('lists only the answers belonging to the requested application', function () {
    $application = ApplicationFactory::new()->create();
    $answer = ApplicationAnswerFactory::new()->for($application)->create();
    ApplicationAnswerFactory::new()->create();

    $this->getJson(route('applications.application-answers.index', $application))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $answer->id);
});

test('creates a department through its named resource route', function () {
    $response = $this->postJson(route('departments.store'), [
        'name' => 'Operations',
        'code' => 'OPS',
        'is_active' => true,
    ]);

    $response->assertCreated()->assertJsonPath('data.name', 'Operations');
    $this->assertDatabaseHas('departments', [
        'id' => $response->json('data.id'),
        'name' => 'Operations',
        'code' => 'OPS',
        'is_active' => true,
    ]);
});

test('updates a department using resource update methods', function (string $method) {
    $department = Department::factory()->create();

    $this->json($method, route('departments.update', $department), [
        'name' => 'Operations',
        'code' => 'OPS',
        'is_active' => true,
    ])->assertOk()->assertJsonPath('data.name', 'Operations');

    $this->assertDatabaseHas('departments', [
        'id' => $department->id,
        'name' => 'Operations',
        'code' => 'OPS',
        'is_active' => true,
    ]);
})->with(['PATCH', 'PUT']);

test('reaches position validation through the named endpoint', function (string $name, string $model) {
    $record = $model::factory()->create(['sort_order' => 1]);

    $this->patchJson(route($name, $record), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('sort_order');

    expect($record->fresh()->sort_order)->toBe(1);
})->with([
    'sections' => ['form-sections.position.update', FormSection::class],
    'fields' => ['form-fields.position.update', FormField::class],
    'options' => ['form-field-options.position.update', FormFieldOption::class],
]);

test('returns 401 for a resource request without an API key', function () {
    $this->withHeaders(['X-API-Key' => ''])
        ->getJson('/api/departments')
        ->assertUnauthorized();
});
