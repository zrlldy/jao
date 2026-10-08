<?php

use App\Models\ApplicationInvitation;
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
            $table->string('application_no');
            $table->foreignUuidFor(ApplicationInvitation::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(JobHiring::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(FormVersion::class)->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('reviewed_by')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
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
