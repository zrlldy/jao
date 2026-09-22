<?php

namespace Database\Seeders;

//use App\Models\User;

use App\Models\FormSection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //        User::factory()->create([
        //            'name' => 'Test User',
        //            'email' => 'test@example.com',
        //        ]);

        $this->call([
            EmploymentTypeSeed::class,
            DepartmentSeed::class,
            LocationSeed::class,
            JobHiringSeed::class,
            ApplicationInvitationSeed::class,
            FormTemplateSeed::class,
            FormVersionSeed::class,
            FormSectionSeeder::class,
            FormFieldSeed::class,
            FormFieldOptionSeed::class,
            JobHiringFormVersionSeed::class
        ]);
    }
}
