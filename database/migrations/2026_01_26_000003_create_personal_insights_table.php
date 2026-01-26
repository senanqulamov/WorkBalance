<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('insight_type', 50)->index();
            $table->string('title');
            $table->text('description');
            $table->json('insight_data')->nullable();

            $table->timestamp('generated_at')->index();
            $table->timestamp('acknowledged_at')->nullable()->index();

            $table->timestamps();

            $table->index(['user_id', 'generated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_insights');
    }
};
