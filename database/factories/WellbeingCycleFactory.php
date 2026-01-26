<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use App\Models\WellbeingCycle;
use Illuminate\Database\Eloquent\Factories\Factory;

class WellbeingCycleFactory extends Factory
{
    protected $model = WellbeingCycle::class;

    public function definition(): array
    {
        return [
            'employee_id' => User::factory(),
            'started_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completed_at' => $this->faker->optional(0.6)->dateTimeBetween('now', '+7 days'),
            'status' => $this->faker->randomElement(['active', 'completed', 'paused']),
            'cycle_type' => $this->faker->randomElement(['daily', 'weekly', 'intervention']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'completed_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
