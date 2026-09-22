<?php

namespace Database\Factories;

use App\Models\FormField;
use App\Models\FormSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormField>
 */
class FormFieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_section_id' => FormSection::factory(),
            'key' => fake()->word(),
            'type' => fake()->randomElement([
                'text',
                'textarea',
                'select',
                'radio',
                'checkbox',
                'date',
            ]),
            'label' => fake()->word(),
            'placeholder' => fake()->sentence(),
            'default_value' => fake()->optional()->word(),
            'is_required' => fake()->boolean(),
            'sort_order' => fake()->numberBetween(1, 10),

            'settings' => [
                'options' => fake()->words(3),
            ],

            'validation_rules' => [
                'required',
                'string',
            ],
        ];
    }
}
