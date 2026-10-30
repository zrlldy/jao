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
        Schema::table('application_answers', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_mime', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('application_answers')->whereNotNull('file_path')->exists()) {
            throw new RuntimeException('Cannot remove file metadata while uploaded files exist.');
        }

        Schema::table('application_answers', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name', 'file_mime', 'file_size']);
        });
    }
};
