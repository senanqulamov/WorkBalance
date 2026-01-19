<?php

use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an order with multiple items and calculates total correctly', function () {
    $user = User::factory()->create();

    // Create a market for the products / order items
    $market = Market::factory()->create();

    $products = Product::factory()->count(3)->create([
        // ensure predictable values
        'price' => 10.00,
        'market_id' => $market->id,
    ]);

    $order = Order::create([
        'order_number' => 'ORD-TEST-'.uniqid(),
        'user_id' => $user->id,
        'status' => 'processing',
        'total' => 0,
    ]);

    $expectedTotal = 0;
    foreach ($products as $i => $product) {
        $qty = $i + 1; // 1,2,3
        $subtotal = $qty * (float) $product->price;
        $order->items()->create([
            'product_id' => $product->id,
            'market_id' => $market->id,
            'quantity' => $qty,
            'unit_price' => $product->price,
            'subtotal' => $subtotal,
        ]);
        $expectedTotal += $subtotal;
    }

    $order->recalcTotal();

    expect((float) $order->refresh()->total)->toBe((float) $expectedTotal);
    expect($order->items)->toHaveCount(3);
});

it('updates order items and refreshes total', function () {
    $user = User::factory()->create();

    // Use a single market for both products
    $market = Market::factory()->create();

    $p1 = Product::factory()->create([
        'price' => 5.00,
        'market_id' => $market->id,
    ]);
    $p2 = Product::factory()->create([
        'price' => 2.50,
        'market_id' => $market->id,
    ]);

    $order = Order::create([
        'order_number' => 'ORD-TEST-'.uniqid(),
        'user_id' => $user->id,
        'status' => 'processing',
        'total' => 0,
    ]);

    $order->items()->create([
        'product_id' => $p1->id,
        'market_id' => $market->id,
        'quantity' => 2,
        'unit_price' => $p1->price,
        'subtotal' => 10.00,
    ]);

    $order->items()->create([
        'product_id' => $p2->id,
        'market_id' => $market->id,
        'quantity' => 4,
        'unit_price' => $p2->price,
        'subtotal' => 10.00,
    ]);

    $order->recalcTotal();
    expect((float) $order->total)->toBe(20.00);

    // Change quantities
    $item = $order->items()->where('product_id', $p2->id)->first();
    $item->forceFill([
        'quantity' => 2,
        'subtotal' => 5.00,
    ])->save();

    $order->recalcTotal();
    expect((float) $order->refresh()->total)->toBe(15.00);
});
