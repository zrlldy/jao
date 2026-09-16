<?php

namespace Database\Factories;

use App\Models\FormTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormTemplate>
 */
class FormTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' =>  fake()->sentence(3),
            'is_active' => fake()->boolean(90)
        ];
    }
}
