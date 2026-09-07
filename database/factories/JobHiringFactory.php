<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\JobHiring;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $title = $this->faker->jobTitle();

        return [
            'department_id' => fn() => Department::inRandomOrder()->value('id'),
            'location_id' => fn() => Location::inRandomOrder()->value('id'),
            'employment_type_id' => fn() => EmploymentType::inRandomOrder()->value('id'),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numerify('####'),
            'description' => $this->faker->text(),
            'requirements' => $this->faker->text(),
            'status' => $this->faker->randomElement(['Available', 'Closed', 'Not Available']),
            'published_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
            'closed_at' => $this->faker->dateTimeBetween('now', '+1 years'),

        ];
    }
}
