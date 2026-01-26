<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\RequestItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuoteItem>
 */
class QuoteItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuoteItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);
        $quantity = $this->faker->numberBetween(1, 100);
        $taxRate = $this->faker->randomElement([0, 5, 10, 15, 20]);
        $subtotal = $unitPrice * $quantity;
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax;

        return [
            'quote_id' => Quote::factory(),
            'request_item_id' => RequestItem::factory(),
            'description' => $this->faker->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'notes' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (QuoteItem $quoteItem) {
            // Additional configuration if needed
        })->afterCreating(function (QuoteItem $quoteItem) {
            // Additional actions after creation if needed
        });
    }
}
