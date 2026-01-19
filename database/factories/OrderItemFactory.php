<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create();

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'market_id' => $product->market_id,
            'quantity' => fake()->numberBetween(1, 5),
            'unit_price' => fake()->randomFloat(2, 5, 500),
            'subtotal' => function (array $attrs) {
                $qty = $attrs['quantity'];
                $unit = $attrs['unit_price'];
                return round($qty * $unit, 2);
            },
        ];
    }
}
