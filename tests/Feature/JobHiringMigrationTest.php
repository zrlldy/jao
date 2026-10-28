<?php

use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\Location;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

uses(LazilyRefreshDatabase::class);

test('migrates existing job hiring references to their string values', function () {
    $department = Department::factory()->create(['name' => 'Operations']);
    $location = Location::factory()->create(['name' => 'Cebu City']);
    $employmentType = EmploymentType::factory()->create(['name' => 'Full-time']);

    Schema::disableForeignKeyConstraints();
    Schema::drop('job_hirings');
    Schema::create('job_hirings', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('department_id')->constrained();
        $table->foreignUuid('location_id')->constrained();
        $table->foreignUuid('employment_type_id')->constrained();
        $table->string('title');
        $table->string('slug')->unique();
        $table->text('description');
        $table->text('requirements');
        $table->text('status');
        $table->date('published_at');
        $table->date('closed_at');
        $table->timestamps();
    });
    Schema::enableForeignKeyConstraints();

    $jobId = (string) Str::uuid();
    DB::table('job_hirings')->insert([
        'id' => $jobId,
        'department_id' => $department->id,
        'location_id' => $location->id,
        'employment_type_id' => $employmentType->id,
        'title' => 'Operations Manager',
        'slug' => 'operations-manager',
        'description' => 'Manage daily operations.',
        'requirements' => 'At least three years of experience.',
        'status' => 'draft',
        'published_at' => '2026-10-28',
        'closed_at' => '2026-11-28',
    ]);

    (require database_path('migrations/2026_10_28_063538_migrate_job_hiring_details_to_strings.php'))->up();

    $this->assertDatabaseHas('job_hirings', [
        'id' => $jobId,
        'department' => 'Operations',
        'location' => 'Cebu City',
        'employment_type' => 'full-time',
    ]);
    expect(Schema::hasColumn('job_hirings', 'department_id'))->toBeFalse();
});
