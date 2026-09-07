<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                    'Human Resources',
                    'Information Technology',
                    'Marketing',
                    'Sales',
                    'Finance',
                    'Operations',
                ]
            ),
            'code' => $this->faker->unique()->numerify('DEPT-####'),
            'is_active' => $this->faker->boolean(),

        ];
    }
}
