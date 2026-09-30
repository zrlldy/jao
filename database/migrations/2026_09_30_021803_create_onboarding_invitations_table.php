<?php

use App\Models\Onboarding;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('onboarding_invitations', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuidFor(Onboarding::class)->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('token');
            $table->date('expired_at');
            $table->date('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboarding_invitations');
    }
};
