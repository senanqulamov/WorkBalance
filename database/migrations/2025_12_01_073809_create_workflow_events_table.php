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
        Schema::create('workflow_events', function (Blueprint $table) {
            $table->id();
            $table->morphs('eventable'); // Polymorphic relationship to allow events to be associated with different models
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type'); // e.g., 'status_changed', 'assigned', 'sla_reminder'
            $table->string('from_state')->nullable(); // Previous state (if applicable)
            $table->string('to_state')->nullable(); // New state (if applicable)
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional data related to the event
            $table->timestamp('occurred_at'); // When the event occurred
            $table->timestamps();

            $table->index('event_type');
            $table->index('occurred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_events');
    }
};
