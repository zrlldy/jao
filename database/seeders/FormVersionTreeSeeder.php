<?php

namespace Database\Seeders;

use App\Models\FormField;
use App\Models\FormFieldOption;
use App\Models\FormSection;
use App\Models\FormVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormVersionTreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormVersion::factory()
            ->count(10)
            ->state(['status' => 'published'])
            ->create()
            ->each(function (FormVersion $formVersion) {
                FormSection::factory()
                    ->count(3)
                    ->create(['form_version_id' => $formVersion->id])
                    ->each(function (FormSection $section) {
                        FormField::factory()
                            ->count(rand(2, 5))
                            ->create(['form_section_id' => $section->id])
                            ->each(function (FormField $field) {
                                if (in_array($field->type, ['select', 'radio', 'checkbox'], true)) {
                                    FormFieldOption::factory()
                                        ->count(3)
                                        ->create(['form_field_id' => $field->id]);
                                }
                            });
                    });
            });
    }
}
