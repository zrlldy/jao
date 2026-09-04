<?php

use App\Models\FormVersion;
use App\Models\JobHiring;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_form_version_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(JobHiring::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(FormVersion::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_form_version_assignments');
    }
};
