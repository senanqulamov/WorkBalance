<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Request;
use App\Models\RequestItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RequestItem>
 */
class RequestItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequestItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'request_id' => Request::factory(),
            'product_name' => $this->faker->words(3, true),
            'quantity' => $this->faker->numberBetween(1, 100),
            'specifications' => $this->faker->optional(0.7)->paragraph(),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (RequestItem $requestItem) {
            // Additional configuration if needed
        })->afterCreating(function (RequestItem $requestItem) {
            // Additional actions after creation if needed
        });
    }
}
