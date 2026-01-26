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
        Schema::create('emotional_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wellbeing_cycle_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->integer('mood_level')->nullable(); // 1-5 scale
            $table->integer('energy_level')->nullable(); // 1-5 scale
            $table->integer('stress_level')->nullable(); // 1-5 scale
            $table->text('private_note')->nullable(); // NEVER accessible to employers
            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->index(['employee_id', 'checked_in_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emotional_check_ins');
    }
};
