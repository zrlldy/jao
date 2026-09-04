<?php

use App\Models\RequirementSubmission;
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
        Schema::create('requirement_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(RequirementSubmission::class)->constrained()->cascadeOnDelete();
            $table->string('reviewed_by');
            $table->string('decision');
            $table->string('remark');
            $table->date('reviewed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_reviews');
    }
};
