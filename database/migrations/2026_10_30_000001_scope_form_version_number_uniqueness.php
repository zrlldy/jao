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
        Schema::table('form_versions', function (Blueprint $table) {
            $table->dropUnique('form_versions_version_unique');
            $table->unique(['form_template_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('form_versions')->select('version')->groupBy('version')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Cannot restore global version uniqueness while templates reuse version numbers.');
        }

        Schema::table('form_versions', function (Blueprint $table) {
            $table->dropUnique(['form_template_id', 'version']);
            $table->unique('version');
        });
    }
};
