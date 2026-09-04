<?php

use App\Models\JobHiring;
use App\Models\OnboardingTemplate;
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
        Schema::create('job_onboarding_template_version', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(OnboardingTemplate::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(JobHiring::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_onboarding_template_version');
    }
};
