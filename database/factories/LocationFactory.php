<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->address(),
            'code' => $this->faker->unique()->numerify('LOC-####'),
            'address' => $this->faker->address(),
            'is_active' => $this->faker->boolean(),
            'province' => $this->faker->city(),
            'hub' => $this->faker->randomElement(['Hub A', 'Hub B', 'Hub C']),
        ];
    }
}
