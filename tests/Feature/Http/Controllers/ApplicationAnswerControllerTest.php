<?php

use App\Http\Middleware\VerifyHmacSignature;
use App\Models\FormField;
use Database\Factories\ApplicationAnswerFactory;
use Database\Factories\ApplicationFactory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyHmacSignature::class);
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'answer-test-key',
    ]);
    $this->withHeaders(['X-API-Key' => 'answer-test-key']);
});

test('a private caller can download a file answer without exposing its storage path', function () {
    Storage::fake('local');
    $application = ApplicationFactory::new()->create();
    $field = FormField::factory()->create(['type' => 'file']);
    $path = 'application-answers/test/resume.pdf';
    Storage::disk('local')->put($path, '%PDF-1.4 test');
    $answer = ApplicationAnswerFactory::new()->for($application)->for($field)->create([
        'value' => null,
        'file_path' => $path,
        'file_name' => 'resume.pdf',
        'file_mime' => 'application/pdf',
        'file_size' => 13,
    ]);

    $this->getJson(route('applications.application-answers.show', [$application, $answer]))
        ->assertOk()
        ->assertJsonPath('data.file.name', 'resume.pdf')
        ->assertDontSee($path);

    $this->get(route('applications.application-answers.file', [$application, $answer]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff');

    $otherApplication = ApplicationFactory::new()->create();
    $this->get(route('applications.application-answers.file', [$otherApplication, $answer]))
        ->assertNotFound();

    $this->withHeaders(['X-API-Key' => 'invalid'])
        ->get(route('applications.application-answers.file', [$application, $answer]))
        ->assertUnauthorized();
});
