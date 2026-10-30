<?php

use App\Models\JobHiring;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('database seeding creates publicly visible job hirings', function () {
    $this->seed(DatabaseSeeder::class);

    expect(JobHiring::publiclyVisible()->count())->toBeGreaterThan(0);
});
