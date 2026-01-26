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
        Schema::create('human_events', function (Blueprint $table) {
            $table->id();
            $table->string('eventable_type');
            $table->unsignedBigInteger('eventable_id');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type'); // check_in_completed, path_started, burnout_threshold_crossed, etc.
            $table->string('from_state')->nullable();
            $table->string('to_state')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable(); // NEVER contains PII
            $table->timestamps();

            $table->index(['eventable_type', 'eventable_id']);
            $table->index(['team_id', 'occurred_at']);
            $table->index('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('human_events');
    }
};
