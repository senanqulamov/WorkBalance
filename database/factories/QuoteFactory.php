<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);
        $quantity = $this->faker->numberBetween(1, 10);
        $totalPrice = $unitPrice * $quantity;
        $totalAmount = $totalPrice; // Can be different if there are multiple items

        return [
            'request_id' => null, // Must be set explicitly
            'supplier_id' => null, // Must be set explicitly; do not auto-create users
            'supplier_invitation_id' => null, // Will be set when creating with invitation
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'total_amount' => $totalAmount,
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'valid_until' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'notes' => $this->faker->optional(0.7)->paragraph(),
            'terms_conditions' => $this->faker->optional(0.5)->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected']),
            'submitted_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the quote is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Indicate that the quote is accepted.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function accepted(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted',
            ];
        });
    }

    /**
     * Indicate that the quote is rejected.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function rejected(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
            ];
        });
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Quote $quote) {
            // Create quote items for this quote if needed
            // This will be handled by the seeder
        });
    }
}
