<?php

namespace Database\Factories;

use App\Models\ApplicationAnswer;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationAnswer>
 */
class ApplicationAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'application_id' => ApplicationFactory::new(),
            'form_field_id' => FormField::factory(),
            'value' => fake()->sentence(),
        ];
    }
}
