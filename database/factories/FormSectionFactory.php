<?php

namespace Database\Factories;

use App\Models\FormSection;
use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormSection>
 */
class FormSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_version_id' => FormVersion::factory(),
            'title' => fake()->title(),
            'description' => fake()->sentence(),
            'sort_order' => fake()->randomNumber()
        ];
    }
}
