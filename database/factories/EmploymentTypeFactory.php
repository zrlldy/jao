<?php

namespace Database\Factories;

use App\Models\EmploymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmploymentType>
 */
class EmploymentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = EmploymentType::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Full-time',
                'Part-time',
                'Contract',
                'Temporary',
            ]),
            'code' => $this->faker->numerify('EMP-####'),
        ];
    }
}
