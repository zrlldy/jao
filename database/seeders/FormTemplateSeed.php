<?php

namespace Database\Seeders;

use App\Models\FormTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormTemplateSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormTemplate::factory(10)->create();
    }
}
