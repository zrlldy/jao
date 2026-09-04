<?php

use App\Models\OnboardingTemplateVersion;
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
        Schema::create('onboarding_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(OnboardingTemplateVersion::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description');
            $table->boolean('is_required')->default(true);
            $table->integer('max_submissions')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('settings');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_requirements');
    }
};
