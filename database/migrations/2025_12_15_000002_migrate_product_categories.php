<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Map existing product categories to category_id
        $categories = DB::table('categories')->pluck('id', 'name');
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            if ($product->category && isset($categories[$product->category])) {
                DB::table('products')->where('id', $product->id)->update([
                    'category_id' => $categories[$product->category],
                ]);
            }
        }
    }

    public function down(): void
    {
        // No action needed
    }
};
