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
        $jobHiringIds = JobHiring::pluck('id');
        $formVersionIds = FormVersion::where('status', 'published')->pluck('id');

        if ($jobHiringIds->isEmpty() || $formVersionIds->isEmpty()) {
            $this->command?->warn('Run JobHiringSeed and FormVersionTreeSeeder first.');
            return;
        }

        foreach ($jobHiringIds as $jobHiringId) {
            $randomFormVersionIds = $formVersionIds->random(min(2, $formVersionIds->count()));

            JobHiring::find($jobHiringId)
                ->formVersions()
                ->syncWithoutDetaching($randomFormVersionIds);
        }
    }
}
