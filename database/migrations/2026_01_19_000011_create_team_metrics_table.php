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
        Schema::create('team_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->date('metric_date');
            $table->integer('cohort_size')->default(0); // Must be >= 5 for privacy
            $table->string('stress_trend')->nullable(); // rising, steady, cooling
            $table->decimal('engagement_rate', 5, 2)->nullable(); // 0.00 to 100.00
            $table->string('burnout_risk_level')->nullable(); // low, moderate, elevated, high
            $table->decimal('check_in_participation', 5, 2)->nullable(); // 0.00 to 100.00
            $table->integer('paths_completed')->default(0);
            $table->decimal('average_intensity_shift', 5, 2)->nullable(); // Change in emotional intensity
            $table->timestamps();

            $table->index(['team_id', 'metric_date']);
            $table->unique(['team_id', 'metric_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_metrics');
    }
};
