<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // page_view, create, update, delete, login, logout, etc.
            $table->string('action')->nullable(); // users.create, orders.update, etc.
            $table->string('model')->nullable(); // User, Order, Product, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('message');
            $table->json('metadata')->nullable(); // Additional data like IP, user agent, etc.
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
