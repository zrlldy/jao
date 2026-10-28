<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\ApplicationInvitation;
use App\Models\FormVersion;
use App\Models\JobHiring;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_invitation_id' => ApplicationInvitation::factory()->for(JobHiring::factory()),
            'job_hiring_id' => fn (array $attributes): string => ApplicationInvitation::findOrFail($attributes['application_invitation_id'])->job_hiring_id,
            'form_version_id' => FormVersion::factory(),
            'status' => 'on_review',
        ];
    }
}
