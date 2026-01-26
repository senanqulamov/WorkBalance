<?php

namespace Database\Factories;

use App\Models\EmotionalCheckIn;
use App\Models\User;
use App\Models\WellbeingCycle;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmotionalCheckInFactory extends Factory
{
    protected $model = EmotionalCheckIn::class;

    public function definition(): array
    {
        return [
            'wellbeing_cycle_id' => null,
            'employee_id' => User::factory(),
            'mood_level' => $this->faker->numberBetween(1, 5),
            'energy_level' => $this->faker->numberBetween(1, 5),
            'stress_level' => $this->faker->numberBetween(1, 5),
            'private_note' => $this->faker->optional(0.3)->paragraph(),
            'checked_in_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ];
    }

    public function stressed(): static
    {
        return $this->state(fn (array $attributes) => [
            'stress_level' => $this->faker->numberBetween(4, 5),
            'mood_level' => $this->faker->numberBetween(1, 2),
            'energy_level' => $this->faker->numberBetween(1, 3),
        ]);
    }

    public function calm(): static
    {
        return $this->state(fn (array $attributes) => [
            'stress_level' => $this->faker->numberBetween(1, 2),
            'mood_level' => $this->faker->numberBetween(4, 5),
            'energy_level' => $this->faker->numberBetween(4, 5),
        ]);
    }
}
