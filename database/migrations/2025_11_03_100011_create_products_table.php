<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->string('category')->nullable();
            $table->timestamps();

            $table->index('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
