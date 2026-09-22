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

        // $this->call([
        //     EmploymentTypeSeed::class,
        //     DepartmentSeed::class,
        //     LocationSeed::class,
        //     JobHiringSeed::class,
        //     ApplicationInvitationSeed::class,
        //     FormTemplateSeed::class,
        //     FormVersionSeed::class,
        //     FormSectionSeeder::class,
        //     FormFieldSeed::class,
        //     FormFieldOptionSeed::class,
        //     JobHiringFormVersionSeed::class
        // ]);
        //
        $this->call([
            EmploymentTypeSeed::class,
            DepartmentSeed::class,
            LocationSeed::class,
            JobHiringSeed::class,            // needs Department, Location, EmploymentType

            FormVersionTreeSeeder::class,    // builds complete, standalone forms (template -> version -> sections -> fields -> options)
            // doesn't need JobHiring at all — replaces the 5 disconnected seeders below

            JobHiringFormVersionSeed::class, // attaches existing forms to existing job hirings — needs BOTH JobHiringSeed and FormVersionTreeSeeder to have run first

            ApplicationInvitationSeed::class, // needs JobHiring to exist (see note below)
        ]);
    }
}
