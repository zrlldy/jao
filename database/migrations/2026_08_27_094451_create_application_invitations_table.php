<?php

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
        Schema::create('application_invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(JobHiring::class)->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('token');
            $table->dateTime('used_at')->nullable();
            $table->date('expired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_invitations');
    }
};
