<?php

namespace Database\Seeders;

use App\Models\FormVersion;
use App\Models\JobHiring;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobHiringFormVersionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobHiring = JobHiring::factory()->create();
        $formVersions = FormVersion::factory(3)->create();
        $jobHiring->formVersions()->attach(
            $formVersions->pluck('id')
        );
    }
}
