<?php

use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\Location;
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
        Schema::create('job_hirings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuidFor(Department::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(Location::class)->constrained()->cascadeOnDelete();
            $table->foreignUuidFor(EmploymentType::class)->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements');
            $table->text('status');
            $table->date('published_at');
            $table->date('closed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_hirings');
    }
};
