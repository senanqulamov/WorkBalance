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
        Schema::table('quote_items', function (Blueprint $table) {
            $table->string('description')->after('request_item_id');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('unit_price');
            $table->dropColumn('total_price'); // Will be calculated dynamically
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropColumn(['description', 'tax_rate']);
            $table->decimal('total_price', 10, 2)->after('quantity');
        });
    }
};
