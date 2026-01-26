<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyCheckInFactory extends Factory
{
    public function definition(): array
    {
        $stressValue = fake()->numberBetween(1, 5);
        $energyValue = fake()->numberBetween(1, 5);

        return [
            'user_id' => User::factory(),
            'stress_level' => match(true) {
                $stressValue <= 2 => 'low',
                $stressValue >= 4 => 'high',
                default => 'medium',
            },
            'stress_value' => $stressValue,
            'energy_level' => match(true) {
                $energyValue <= 2 => 'low',
                $energyValue >= 4 => 'high',
                default => 'medium',
            },
            'energy_value' => $energyValue,
            'mood_state' => fake()->randomElement(['great', 'good', 'okay', 'low', 'struggling']),
            'optional_note' => fake()->optional(0.3)->sentence(),
            'check_in_date' => fake()->dateTimeBetween('-60 days', '-3 days'), // 48-hour delay enforced
        ];
    }

    public function highStress(): static
    {
        return $this->state(fn (array $attributes) => [
            'stress_level' => 'high',
            'stress_value' => fake()->numberBetween(4, 5),
            'energy_level' => 'low',
            'energy_value' => fake()->numberBetween(1, 2),
        ]);
    }

    public function healthy(): static
    {
        return $this->state(fn (array $attributes) => [
            'stress_level' => 'low',
            'stress_value' => fake()->numberBetween(1, 2),
            'energy_level' => 'high',
            'energy_value' => fake()->numberBetween(4, 5),
            'mood_state' => 'great',
        ]);
    }
}
