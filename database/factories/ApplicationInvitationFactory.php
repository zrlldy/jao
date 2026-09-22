<?php

namespace Database\Factories;

use App\Models\ApplicationInvitation;
use App\Models\JobHiring;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use function Symfony\Component\Clock\now;

/**
 * @extends Factory<ApplicationInvitation>
 */
class ApplicationInvitationFactory extends Factory
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
            'email' => fake()->email(),
            'token' => Str::random(64),
            'expired_at' => Carbon::now()->addDays(7),
        ];
    }
}
