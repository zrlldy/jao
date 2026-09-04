<?php

use App\Models\Onboarding;
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
        Schema::create('onboarding_require_instances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Onboarding::class)->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->integer('submission_count')->default(0);
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_require_instances');
    }
};
