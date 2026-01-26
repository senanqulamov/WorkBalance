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
        Schema::create('activity_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action_type'); // page_view, feature_used, interaction, etc.
            $table->text('description')->nullable();
            $table->string('context')->nullable(); // humanops, workbalance
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable(); // NEVER contains emotional or personal data
            $table->timestamps();

            $table->index(['team_id', 'occurred_at']);
            $table->index('action_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_signals');
    }
};
