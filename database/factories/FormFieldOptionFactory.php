<?php

namespace Database\Factories;

use App\Models\FormField;
use App\Models\FormFieldOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormFieldOption>
 */
class FormFieldOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_field_id' => FormField::factory(),
            'label' => fake()->word(),
            'value' => fake()->word(),
            'sort_order' => fake()->numberBetween(1, 10)
        ];
    }
}
