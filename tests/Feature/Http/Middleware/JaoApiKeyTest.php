<?php

use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config()->set([
        'services.api.header' => 'X-API-Key',
        'services.jao.api_key' => 'test-server-key',
    ]);

    Route::get('/api/test-key', fn () => response()->json(['authorized' => true]))
        ->middleware('jao.key');
});

test('accepts the configured API key', function () {
    $this->getJson('/api/test-key', ['X-API-Key' => 'test-server-key'])
        ->assertOk()
        ->assertExactJson(['authorized' => true]);
});

test('uses X-API-Key when the header setting is missing', function () {
    config()->set('services.api.header', null);

    $this->getJson('/api/test-key', ['X-API-Key' => 'test-server-key'])
        ->assertOk();
});

test('accepts a custom header name', function () {
    config()->set('services.api.header', 'X-Jao-Key');

    $this->getJson('/api/test-key', ['X-Jao-Key' => 'test-server-key'])
        ->assertOk();
});

test('returns 401 for missing or incorrect API keys', function (array $headers) {
    $this->getJson('/api/test-key', $headers)
        ->assertUnauthorized()
        ->assertExactJson(['message' => 'Unauthorized.']);
})->with([
    'missing' => [[]],
    'empty' => [['X-API-Key' => '']],
    'incorrect' => [['X-API-Key' => 'wrong-key']],
    'prefix match' => [['X-API-Key' => 'test-server-key-extra']],
]);

test('returns 401 when the configured API key is invalid', function (mixed $key) {
    config()->set('services.jao.api_key', $key);

    $this->getJson('/api/test-key', ['X-API-Key' => ''])
        ->assertUnauthorized();
})->with([null, '', ' ', false, ['invalid']]);

test('returns 401 when the configured header is invalid', function (mixed $header) {
    config()->set('services.api.header', $header);

    $this->getJson('/api/test-key', ['X-API-Key' => 'test-server-key'])
        ->assertUnauthorized();
})->with(['', ' ', false, ['invalid']]);

test('does not accept an API key from a query parameter', function () {
    $this->getJson('/api/test-key?api_key=test-server-key')
        ->assertUnauthorized();
});
