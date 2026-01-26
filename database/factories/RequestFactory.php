<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Request::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'buyer_id' => null, // Must be set explicitly; do not auto-create users
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraphs(asText: true),
            'deadline' => $this->faker->dateTimeBetween('+3 days', '+6 weeks'),
            'status' => $this->faker->randomElement([
                RequestStatus::DRAFT->value,
                RequestStatus::OPEN->value,
                RequestStatus::CLOSED->value,
                RequestStatus::AWARDED->value,
                RequestStatus::CANCELLED->value,
            ]),
        ];
    }

    /**
     * Indicate that the request is in draft status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function draft(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => RequestStatus::DRAFT->value,
            ];
        });
    }

    /**
     * Indicate that the request is in open status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function open(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => RequestStatus::OPEN->value,
            ];
        });
    }

    /**
     * Indicate that the request is in closed status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function closed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => RequestStatus::CLOSED->value,
            ];
        });
    }

    /**
     * Indicate that the request is in awarded status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function awarded(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => RequestStatus::AWARDED->value,
            ];
        });
    }

    /**
     * Indicate that the request is in cancelled status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => RequestStatus::CANCELLED->value,
            ];
        });
    }
}
