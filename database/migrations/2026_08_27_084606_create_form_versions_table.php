<?php

use App\Models\FormTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('form_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(FormTemplate::class)
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('version')->unique();
            $table->string('status');
            $table->date('published_at')->nullable();
            $table->timestamps();
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_versions');
    }
};
