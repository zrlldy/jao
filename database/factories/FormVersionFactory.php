<?php

namespace Database\Factories;

use App\Models\FormTemplate;
use App\Models\FormVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormVersion>
 */
class FormVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement([
            'published',
            'draft',
            'archived',
        ]);
        return [
            'form_template_id' => FormTemplate::factory(),
            'version' => fake()->randomNumber(),
            'status' => $status,
            'published_at' => $status === 'draft'
                ? null
                : fake()->dateTime(timezone: 'Asia/Manila'),
        ];
    }
}
