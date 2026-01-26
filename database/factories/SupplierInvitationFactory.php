<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\SupplierInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupplierInvitation>
 */
class SupplierInvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupplierInvitation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sentAt = $this->faker->dateTimeBetween('-2 months', 'now');

        // Weighted status distribution: more accepted, some pending, few declined
        $statusPool = array_merge(
            array_fill(0, 5, 'accepted'),
            array_fill(0, 3, 'pending'),
            array_fill(0, 2, 'declined'),
        );
        $status = $this->faker->randomElement($statusPool);

        $respondedAt = null;
        if ($status !== 'pending') {
            $respondedAt = $this->faker->dateTimeBetween($sentAt, 'now');
        }

        return [
            'request_id' => null, // Must be set explicitly
            'supplier_id' => null, // Must be set explicitly; do not auto-create users
            'status' => $status,
            'sent_at' => $sentAt,
            'responded_at' => $respondedAt,
            'notes' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    /**
     * Indicate that the invitation is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'responded_at' => null,
            ];
        });
    }

    /**
     * Indicate that the invitation is accepted.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function accepted(): Factory
    {
        return $this->state(function (array $attributes) {
            $sentAt = $attributes['sent_at'] ?? $this->faker->dateTimeBetween('-1 month', '-1 day');
            return [
                'status' => 'accepted',
                'sent_at' => $sentAt,
                'responded_at' => $this->faker->dateTimeBetween($sentAt, 'now'),
            ];
        });
    }

    /**
     * Indicate that the invitation is declined.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function declined(): Factory
    {
        return $this->state(function (array $attributes) {
            $sentAt = $attributes['sent_at'] ?? $this->faker->dateTimeBetween('-1 month', '-1 day');
            return [
                'status' => 'declined',
                'sent_at' => $sentAt,
                'responded_at' => $this->faker->dateTimeBetween($sentAt, 'now'),
            ];
        });
    }
}
