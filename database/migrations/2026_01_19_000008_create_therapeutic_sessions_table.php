<?php

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
        Schema::create('therapeutic_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wellbeing_cycle_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('therapeutic_path_id')->constrained()->onDelete('cascade');
            $table->string('situation_type')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed, paused
            $table->integer('intensity_before')->nullable(); // 1-10 scale
            $table->integer('intensity_after')->nullable(); // 1-10 scale
            $table->text('reflection_note')->nullable(); // NEVER accessible to employers
            $table->timestamps();

            $table->index(['employee_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapeutic_sessions');
    }
};
