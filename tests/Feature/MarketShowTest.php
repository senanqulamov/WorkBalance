<?php

use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

it('shows market details and metrics', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $market = Market::factory()->create();
    $product = Product::factory()->create(['market_id' => $market->id, 'price' => 10]);

    $order = Order::factory()->create([
        'market_id' => $market->id,
        'user_id' => $user->id,
        'total' => 10,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
        'subtotal' => 10,
    ]);

    $response = $this->get(route('markets.show', $market));

    $response->assertOk();
    $response->assertSee($market->name);
    $response->assertSee('Orders');
    $response->assertSee('Revenue');
    $response->assertSee($product->name); // product listed
});

it('returns 404 for missing market', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/markets/999999');
    $response->assertNotFound();
});
