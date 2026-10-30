<?php

namespace Database\Factories;

use App\Models\JobHiring;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobHiring>
 */
class JobHiringFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department' => $this->faker->randomElement(['Operations', 'Marketing', 'Finance']),
            'location' => $this->faker->city(),
            'employment_type' => $this->faker->randomElement(['full-time', 'part-time', 'contract', 'internship']),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->text(),
            'requirements' => $this->faker->text(),
            'status' => 'draft',
            'published_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'closed_at' => $this->faker->dateTimeBetween('now', '+1 years'),

        ];
    }
}
