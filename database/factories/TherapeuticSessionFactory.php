<?php

namespace Database\Factories;

use App\Models\TherapeuticSession;
use App\Models\TherapeuticPath;
use App\Models\User;
use App\Models\WellbeingCycle;
use Illuminate\Database\Eloquent\Factories\Factory;

class TherapeuticSessionFactory extends Factory
{
    protected $model = TherapeuticSession::class;

    public function definition(): array
    {
        $intensityBefore = $this->faker->numberBetween(5, 10);

        return [
            'wellbeing_cycle_id' => null,
            'employee_id' => User::factory(),
            'therapeutic_path_id' => TherapeuticPath::factory(),
            'situation_type' => $this->faker->randomElement([
                'deadline_pressure',
                'emotional_exhaustion',
                'conflict',
                'lack_of_motivation',
            ]),
            'started_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'completed_at' => $this->faker->optional(0.7)->dateTimeBetween('now', '+2 days'),
            'status' => $this->faker->randomElement(['in_progress', 'completed', 'paused']),
            'intensity_before' => $intensityBefore,
            'intensity_after' => $this->faker->optional(0.7)->numberBetween(1, max(1, $intensityBefore - 2)),
            'reflection_note' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => now(),
            'intensity_after' => $this->faker->numberBetween(1, 4),
        ]);
    }
}
