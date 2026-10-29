<?php

use App\Http\Middleware\VerifyHmacSignature;
use App\Models\ApplicationInvitation;
use App\Models\FormField;
use App\Models\FormSection;
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
        'services.jao.api_key' => 'job-hiring-test-key',
    ]);

    $this->withHeaders(['X-API-Key' => 'job-hiring-test-key']);
});

test('creates a job hiring with string department and location and an employment type', function () {
    $response = $this->postJson(route('job-hirings.store'), [
        'department' => 'Operations',
        'location' => 'Cebu City',
        'employment_type' => 'full-time',
        'title' => 'Operations Manager',
        'description' => 'Manage daily operations.',
        'requirements' => 'At least three years of experience.',
        'status' => 'draft',
        'published_at' => '2026-10-28',
        'closed_at' => '2026-11-28',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.department', 'Operations')
        ->assertJsonPath('data.location', 'Cebu City')
        ->assertJsonPath('data.employmentType', 'full-time');

    expect($response->json('data.slug'))->toStartWith('operations-manager-');

    $this->assertDatabaseHas('job_hirings', [
        'id' => $response->json('data.id'),
        'slug' => $response->json('data.slug'),
        'department' => 'Operations',
        'location' => 'Cebu City',
        'employment_type' => 'full-time',
    ]);
});

test('creates a draft job hiring without publication or closing dates', function () {
    $response = $this->postJson(route('job-hirings.store'), [
        'department' => 'Operations',
        'location' => 'Cebu City',
        'employment_type' => 'full-time',
        'title' => 'Operations Manager',
        'description' => 'Manage daily operations.',
        'requirements' => 'At least three years of experience.',
        'status' => 'draft',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.published_at', null)
        ->assertJsonPath('data.closed_at', null);

    $this->assertDatabaseHas('job_hirings', [
        'id' => $response->json('data.id'),
        'published_at' => null,
        'closed_at' => null,
    ]);
});

test('generates unique slugs for job hirings with the same title', function () {
    $first = JobHiring::factory()->create(['title' => 'Warehouse Associate']);
    $second = JobHiring::factory()->create(['title' => 'Warehouse Associate']);

    expect($first->slug)->toStartWith('warehouse-associate-')
        ->and($second->slug)->toStartWith('warehouse-associate-')
        ->not->toBe($first->slug);
});

test('keeps the generated slug when the title changes', function () {
    $jobHiring = JobHiring::factory()->create(['title' => 'Warehouse Associate']);
    $slug = $jobHiring->slug;

    $this->patchJson(route('job-hirings.update', $jobHiring), [
        'title' => 'Senior Warehouse Associate',
        'slug' => 'client-supplied-slug',
    ])->assertOk()
        ->assertJsonPath('data.title', 'Senior Warehouse Associate')
        ->assertJsonPath('data.slug', $slug);

    expect($jobHiring->fresh()->slug)->toBe($slug);
});

test('lists and filters job hirings by department string', function () {
    $matchingJob = JobHiring::factory()->create(['department' => 'Operations']);
    JobHiring::factory()->create(['department' => 'Marketing']);

    $this->getJson(route('job-hirings.index', ['department' => 'Operations']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matchingJob->id)
        ->assertJsonPath('data.0.department', 'Operations');

    $this->getJson(route('job-hirings.show', $matchingJob))
        ->assertOk()
        ->assertJsonPath('data.location', $matchingJob->location)
        ->assertJsonPath('data.employmentType', $matchingJob->employment_type);
});

test('public job list shows only published jobs within their display dates', function () {
    $visibleJob = JobHiring::factory()->create([
        'status' => 'published',
        'department' => 'Operations',
        'published_at' => today()->subDay(),
        'closed_at' => today()->addDay(),
    ]);
    JobHiring::factory()->create(['status' => 'draft', 'department' => 'Operations']);
    JobHiring::factory()->create([
        'status' => 'published',
        'department' => 'Operations',
        'published_at' => today()->addDay(),
    ]);
    JobHiring::factory()->create([
        'status' => 'published',
        'department' => 'Operations',
        'closed_at' => today()->subDay(),
    ]);

    $this->withHeaders(['X-API-Key' => ''])
        ->getJson(route('public.job-hirings.index', ['department' => 'Operations']))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $visibleJob->id);
});

test('public job detail hides draft, future, and closed jobs', function () {
    $visibleJob = JobHiring::factory()->create([
        'status' => 'published',
        'published_at' => today()->subDay(),
        'closed_at' => today()->addDay(),
    ]);
    $draftJob = JobHiring::factory()->create(['status' => 'draft']);
    $futureJob = JobHiring::factory()->create([
        'status' => 'published',
        'published_at' => today()->addDay(),
    ]);
    $closedJob = JobHiring::factory()->create([
        'status' => 'published',
        'closed_at' => today()->subDay(),
    ]);

    $this->withHeaders(['X-API-Key' => ''])
        ->getJson(route('public.job-hirings.show', $visibleJob))
        ->assertOk()
        ->assertJsonPath('data.id', $visibleJob->id);

    foreach ([$draftJob, $futureJob, $closedJob] as $hiddenJob) {
        $this->getJson(route('public.job-hirings.show', $hiddenJob))->assertNotFound();
    }
});

test('public jobs allow unset dates and remain visible on the closing date', function () {
    $undatedJob = JobHiring::factory()->create([
        'status' => 'published',
        'published_at' => null,
        'closed_at' => null,
    ]);
    $closingToday = JobHiring::factory()->create([
        'status' => 'published',
        'published_at' => today(),
        'closed_at' => today(),
    ]);

    $this->withHeaders(['X-API-Key' => ''])
        ->getJson(route('public.job-hirings.index'))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['id' => $undatedJob->id])
        ->assertJsonFragment(['id' => $closingToday->id]);
});

test('private job list still includes drafts for HRIS', function () {
    $draftJob = JobHiring::factory()->create(['status' => 'draft']);

    $this->getJson(route('job-hirings.index'))
        ->assertOk()
        ->assertJsonFragment(['id' => $draftJob->id]);
});

test('updates the scalar job hiring fields', function () {
    $jobHiring = JobHiring::factory()->create();

    $this->patchJson(route('job-hirings.update', $jobHiring), [
        'department' => 'Finance',
        'location' => 'Mandaue City',
        'employment_type' => 'contract',
    ])->assertOk()
        ->assertJsonPath('data.department', 'Finance')
        ->assertJsonPath('data.location', 'Mandaue City')
        ->assertJsonPath('data.employmentType', 'contract');

    $this->assertDatabaseHas('job_hirings', [
        'id' => $jobHiring->id,
        'department' => 'Finance',
        'location' => 'Mandaue City',
        'employment_type' => 'contract',
    ]);
});

test('rejects an employment type outside the job hiring enum', function () {
    $jobHiring = JobHiring::factory()->create();

    $this->patchJson(route('job-hirings.update', $jobHiring), [
        'employment_type' => 'temporary',
    ])->assertUnprocessable()->assertJsonValidationErrors('employment_type');

    expect($jobHiring->fresh()->employment_type)->not->toBe('temporary');
});

test('updates a job hiring to an allowed status', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'draft']);

    $this->patchJson(route('job-hirings.update', $jobHiring), [
        'status' => 'published',
    ])->assertOk()->assertJsonPath('data.status', 'published');

    $this->assertDatabaseHas('job_hirings', [
        'id' => $jobHiring->id,
        'status' => 'published',
    ]);
});

test('returns 422 and retains the job hiring status for unsupported text', function () {
    $jobHiring = JobHiring::factory()->create(['status' => 'draft']);

    $this->patchJson(route('job-hirings.update', $jobHiring), [
        'status' => 'on hold',
    ])->assertUnprocessable()->assertJsonValidationErrors('status');

    expect($jobHiring->fresh()->status)->toBe('draft');
});

test('returns 409 and retains applications and answers when deleting a job hiring', function (?string $submittedAt) {
    $jobHiring = JobHiring::factory()->create();
    $formVersion = FormVersion::factory()->create();
    $invitation = ApplicationInvitation::factory()->for($jobHiring)->create();
    $application = ApplicationFactory::new()->for($invitation, 'applicationInvitation')
        ->for($jobHiring)->for($formVersion)->create(['submitted_at' => $submittedAt]);
    $formField = FormField::factory()->for(FormSection::factory()->for($formVersion))->create();
    $answer = ApplicationAnswerFactory::new()->for($application)->for($formField)->create();

    $this->deleteJson(route('job-hirings.destroy', $jobHiring))
        ->assertStatus(409)
        ->assertJsonPath('message', 'Cannot delete a job hiring with applications.');

    $this->assertModelExists($jobHiring);
    $this->assertModelExists($application);
    $this->assertModelExists($answer);
})->with([
    'submitted application' => ['2026-10-28 10:00:00'],
    'saved draft' => null,
]);

test('deletes a job hiring without applications', function () {
    $jobHiring = JobHiring::factory()->create();

    $this->deleteJson(route('job-hirings.destroy', $jobHiring))->assertNoContent();

    $this->assertModelMissing($jobHiring);
});
