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
        Schema::create('path_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('therapeutic_path_id')->constrained()->onDelete('cascade');
            $table->integer('step_order')->default(0);
            $table->string('step_type'); // validation, regulation, insight, action
            $table->string('title');
            $table->text('content')->nullable();
            $table->text('prompt')->nullable();
            $table->text('validation_text')->nullable();
            $table->text('regulation_exercise')->nullable();
            $table->text('insight_text')->nullable();
            $table->text('micro_action')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('path_steps');
    }
};
