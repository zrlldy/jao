<?php

namespace Database\Seeders;

use App\Models\FormVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormVersionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormVersion::factory(10)->create();
    }
}
