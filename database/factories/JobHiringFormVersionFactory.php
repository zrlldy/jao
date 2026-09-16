<?php

namespace Database\Factories;

use App\Models\FormVersion;
use App\Models\JobHiring;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class JobHiringFormVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_hiring_id' => JobHiring::factory(),
            'form_version_id' => FormVersion::factory(),
        ];
    }
}
