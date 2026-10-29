<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            $table->unique('application_invitation_id');
        });

        Schema::table('application_invitations', function (Blueprint $table): void {
            $table->foreignUuid('form_version_id')
                ->nullable()
                ->constrained('form_versions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('application_invitations', function (Blueprint $table): void {
            $table->dropForeign(['form_version_id']);
            $table->dropColumn('form_version_id');
        });

        Schema::table('applications', function (Blueprint $table): void {
            $table->dropUnique(['application_invitation_id']);
        });
    }
};
