<?php

use App\Models\Applicant;
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
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('applicant_no');
            $table->foreignUuidFor(Applicant::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(JobHiring::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(FormVersion::class)->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('reviewed_by')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
