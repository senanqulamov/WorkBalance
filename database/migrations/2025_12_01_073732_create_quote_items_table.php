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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('request_item_id')->constrained('request_items')->cascadeOnDelete();
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('quote_id');
            $table->index('request_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
