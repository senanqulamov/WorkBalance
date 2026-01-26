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
        Schema::create('reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapeutic_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->text('what_changed')->nullable(); // NEVER accessible to employers
            $table->integer('intensity_shift')->nullable(); // Change in emotional intensity
            $table->text('key_insight')->nullable(); // NEVER accessible to employers
            $table->text('next_action')->nullable(); // NEVER accessible to employers
            $table->timestamp('reflected_at');
            $table->timestamps();

            $table->index(['employee_id', 'reflected_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reflections');
    }
};
