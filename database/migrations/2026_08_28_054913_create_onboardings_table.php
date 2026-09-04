<?php

use App\Models\Application;
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
        Schema::create('onboardings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Application::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(OnboardingTemplate::class)->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->date('started_at');
            $table->date('completed_at')->nullable();
            $table->date('deadline_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};
