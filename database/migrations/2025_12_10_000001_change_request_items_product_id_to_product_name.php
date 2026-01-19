<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add product_name column
        Schema::table('request_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('request_id');
        });

        // Migrate existing data: copy product names from products table
        DB::statement('
            UPDATE request_items
            INNER JOIN products ON request_items.product_id = products.id
            SET request_items.product_name = products.name
        ');

        // Make product_name required now that data is migrated
        Schema::table('request_items', function (Blueprint $table) {
            $table->string('product_name')->nullable(false)->change();
        });

        // Drop foreign key constraint, index, and product_id column
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            // Re-add product_id column
            $table->foreignId('product_id')->nullable()->after('request_id')->constrained('products')->cascadeOnDelete();
            $table->index('product_id');
        });

        // Note: We cannot reliably restore product_id from product_name in down()
        // as multiple products may have the same name
    }
};
