<?php

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
        Schema::create('onboarding_template_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(OnboardingTemplate::class)->constrained()->cascadeOnDelete();
            $table->string('version');
            $table->string('status');
            $table->date('published_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_template_versions');
    }
};
