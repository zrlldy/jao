<?php

use App\Models\JobHiring;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('database seeding creates job hirings with generated slugs', function () {
    $this->seed(DatabaseSeeder::class);

    expect(JobHiring::count())->toBeGreaterThan(0);
});
