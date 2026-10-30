<?php

use App\Http\Middleware\VerifyHmacSignature;
use App\Models\FormTemplate;
use App\Models\FormVersion;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(VerifyHmacSignature::class);
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'form-template-test-key',
    ]);
    $this->withHeaders(['X-API-Key' => 'form-template-test-key']);
});

test('retains a template while it has versions', function () {
    $template = FormTemplate::factory()->create();
    $version = FormVersion::factory()->for($template)->create();

    $this->deleteJson(route('form-templates.destroy', $template))->assertStatus(409);

    $this->assertModelExists($template);
    $this->assertModelExists($version);
});
