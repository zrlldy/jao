<?php

namespace Database\Seeders;

use App\Models\FormField;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormFieldSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormField::factory(10)->create();
    }
}
