<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * REMOVES ALL PROCUREMENT-RELATED TABLES
     * These are replaced by wellbeing-focused tables.
     */
    public function up(): void
    {
        // Drop procurement tables in reverse dependency order
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('supplier_invitations');
        Schema::dropIfExists('request_items');
        Schema::dropIfExists('requests');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('market_users');
        Schema::dropIfExists('products');
        Schema::dropIfExists('markets');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('workflow_events');
        Schema::dropIfExists('logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse this migration - procurement schema removed permanently
        // This is intentional transformation from Fluxa to WorkBalance
    }
};
