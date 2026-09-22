<?php

namespace Database\Seeders;

use App\Models\ApplicationInvitation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationInvitationSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApplicationInvitation::factory(10)->create();
    }
}
