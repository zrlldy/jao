<?php

namespace Database\Seeders;

use App\Models\JobHiring;
use Illuminate\Database\Seeder;

class JobHiringSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobHiring::factory()
            ->count(10)
            ->create();
    }
}
