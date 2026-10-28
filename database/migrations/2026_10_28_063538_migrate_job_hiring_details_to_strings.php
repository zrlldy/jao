<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('job_hirings', 'department_id')) {
            return;
        }

        $jobs = DB::table('job_hirings as jobs')
            ->join('departments', 'departments.id', '=', 'jobs.department_id')
            ->join('locations', 'locations.id', '=', 'jobs.location_id')
            ->join('employment_types', 'employment_types.id', '=', 'jobs.employment_type_id')
            ->select('jobs.id', 'departments.name as department', 'locations.name as location', 'employment_types.name as employment_type')
            ->get();

        if ($jobs->count() !== DB::table('job_hirings')->count()) {
            throw new RuntimeException('Job hirings have missing department, location, or employment type references.');
        }

        foreach ($jobs as $job) {
            $job->employment_type = Str::slug($job->employment_type);

            if (! in_array($job->employment_type, ['full-time', 'part-time', 'contract', 'internship'], true)) {
                throw new RuntimeException("Job hiring {$job->id} has an unsupported employment type: {$job->employment_type}.");
            }
        }

        Schema::table('job_hirings', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'internship'])->nullable();
        });

        foreach ($jobs as $job) {
            DB::table('job_hirings')->where('id', $job->id)->update([
                'department' => $job->department,
                'location' => $job->location,
                'employment_type' => $job->employment_type,
            ]);
        }

        Schema::table('job_hirings', function (Blueprint $table) {
            $table->string('department')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->enum('employment_type', ['full-time', 'part-time', 'contract', 'internship'])->nullable(false)->change();
        });

        Schema::table('job_hirings', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['employment_type_id']);
            $table->dropColumn(['department_id', 'location_id', 'employment_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException('The original job hiring UUID references cannot be recovered from string values.');
    }
};
