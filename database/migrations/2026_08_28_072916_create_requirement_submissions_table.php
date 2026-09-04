<?php

use App\Models\OnboardingRequireInstance;
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
        Schema::create('requirement_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(OnboardingRequireInstance::class)->constrained()->cascadeOnDelete();
            $table->string('submission_no');
            $table->text('status')->nullable();
            $table->date('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_submissions');
    }
};
