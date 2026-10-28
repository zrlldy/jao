<?php

use App\Models\JobHiring;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
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
