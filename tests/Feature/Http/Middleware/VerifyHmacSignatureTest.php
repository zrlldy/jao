<?php

use App\Http\Middleware\VerifyHmacSignature;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config()->set('services.jao.hmac_secret', 'test-signing-secret');
    $this->travelTo('2026-10-27 12:00:00 UTC');

    Route::match(['GET', 'POST'], '/api/test-signature', fn () => response()->json(['authorized' => true]))
        ->middleware(VerifyHmacSignature::class);
});

function signedJaoHeaders(
    string $method = 'POST',
    string $uri = '/api/test-signature?status=published',
    string $body = '{"title":"Engineer"}',
    string $timestamp = '1793102400',
    string $nonce = 'b74add62-5d68-434c-9717-924149acb32a',
): array {
    return [
        'X-Timestamp' => $timestamp,
        'X-Nonce' => $nonce,
        'X-Signature' => hash_hmac('sha256', "$method\n$uri\n$timestamp\n$nonce\n$body", 'test-signing-secret'),
    ];
}

test('accepts a valid signed request with a timestamp', function () {
    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], signedJaoHeaders())
        ->assertOk()
        ->assertExactJson(['authorized' => true]);
});

test('a private production route rejects an API key without HMAC headers', function () {
    config()->set('services.jao.api_key', 'test-server-key');

    $this->getJson(route('departments.index'), ['X-API-Key' => 'test-server-key'])
        ->assertUnauthorized()
        ->assertExactJson(['message' => 'Unauthorized.']);
});

test('a signed private production request reaches validation', function () {
    config()->set('services.jao.api_key', 'test-server-key');

    $headers = array_merge(
        ['X-API-Key' => 'test-server-key'],
        signedJaoHeaders(method: 'POST', uri: '/api/job-hirings', body: '[]'),
    );

    $this->postJson(route('job-hirings.store'), [], $headers)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

test('returns 401 for missing or malformed signature headers', function (array $override) {
    $headers = array_replace(signedJaoHeaders(), $override);

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], $headers)
        ->assertUnauthorized()
        ->assertExactJson(['message' => 'Unauthorized.']);
})->with([
    'missing signature' => [['X-Signature' => null]],
    'invalid signature' => [['X-Signature' => str_repeat('0', 64)]],
    'non-hex signature' => [['X-Signature' => str_repeat('z', 64)]],
    'short signature' => [['X-Signature' => 'short']],
    'missing timestamp' => [['X-Timestamp' => null]],
    'empty timestamp' => [['X-Timestamp' => '']],
    'non-numeric timestamp' => [['X-Timestamp' => '1793102400-invalid']],
    'missing nonce' => [['X-Nonce' => null]],
    'invalid nonce' => [['X-Nonce' => 'invalid']],
]);

test('returns 401 when the signing secret is invalid', function (mixed $secret) {
    config()->set('services.jao.hmac_secret', $secret);

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], signedJaoHeaders())
        ->assertUnauthorized();
})->with([null, '', ' ', false]);

test('returns 401 for signatures outside the five minute clock window', function (int $seconds) {
    $headers = signedJaoHeaders(timestamp: (string) (now()->timestamp + $seconds));

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], $headers)
        ->assertUnauthorized();
})->with([-301, 301]);

test('accepts signatures at the five minute clock boundaries', function (int $seconds) {
    $headers = signedJaoHeaders(timestamp: (string) (now()->timestamp + $seconds));

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], $headers)
        ->assertOk();
})->with([-300, 300]);

test('returns 401 when signed request data is changed', function (string $method, string $uri, array $body) {
    $this->json($method, $uri, $body, signedJaoHeaders())
        ->assertUnauthorized();
})->with([
    'method' => ['GET', '/api/test-signature?status=published', ['title' => 'Engineer']],
    'query' => ['POST', '/api/test-signature?status=draft', ['title' => 'Engineer']],
    'body' => ['POST', '/api/test-signature?status=published', ['title' => 'Manager']],
]);

test('returns 401 when a signed request is replayed', function () {
    $headers = signedJaoHeaders();

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], $headers)->assertOk();

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], $headers)
        ->assertUnauthorized();
});

test('keeps a future dated nonce blocked for its entire validity window', function () {
    $headers = signedJaoHeaders(timestamp: (string) (now()->timestamp + 300));

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], $headers)->assertOk();
    $this->travel(301)->seconds();

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], $headers)
        ->assertUnauthorized();
});

test('accepts another request with a fresh nonce in the same second', function () {
    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], signedJaoHeaders())->assertOk();

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], signedJaoHeaders(
        nonce: 'c74add62-5d68-434c-9717-924149acb32a',
    ))->assertOk();
});

test('does not consume the nonce of an invalid signature', function () {
    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Manager'], signedJaoHeaders())
        ->assertUnauthorized();

    $this->json('POST', '/api/test-signature?status=published', ['title' => 'Engineer'], signedJaoHeaders())
        ->assertOk();
});
