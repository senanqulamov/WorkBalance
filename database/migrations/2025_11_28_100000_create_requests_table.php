<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('deadline');
            $table->enum('status', ['draft', 'open', 'closed', 'awarded', 'cancelled'])->default('draft');
            $table->timestamps();

            $table->index('buyer_id');
            $table->index('status');
            $table->index('deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
