<?php

namespace Database\Seeders;

use App\Models\FormFieldOption;
use Illuminate\Database\Seeder;

class FormFieldOptionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormFieldOption::factory(10)->create();
    }
}
