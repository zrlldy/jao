<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_hirings', function (Blueprint $table) {
            $table->date('published_at')->nullable()->change();
            $table->date('closed_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('job_hirings')->whereNull('published_at')->orWhereNull('closed_at')->exists()) {
            throw new RuntimeException('Cannot require job hiring dates while undated job hirings exist.');
        }

        Schema::table('job_hirings', function (Blueprint $table) {
            $table->date('published_at')->nullable(false)->change();
            $table->date('closed_at')->nullable(false)->change();
        });
    }
};
