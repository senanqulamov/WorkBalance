<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationHealthIndexFactory extends Factory
{
    public function definition(): array
    {
        $avgStress = fake()->numberBetween(3, 7);
        $avgEnergy = fake()->numberBetween(4, 8);
        $avgMood = fake()->numberBetween(4, 8);

        $inverseStress = 11 - $avgStress;
        $wellbeingScore = ($inverseStress + $avgEnergy + $avgMood) / 3;
        $burnoutRisk = ($avgStress / 10 + (11 - $avgEnergy) / 10) / 2;

        return [
            'date' => fake()->dateTimeBetween('-90 days', '-2 days'),
            'overall_wellbeing_score' => round($wellbeingScore, 2),
            'burnout_risk_level' => round($burnoutRisk, 2),
            'financial_stress_level' => round(fake()->randomFloat(2, 0.1, 0.6), 2),
            'relationship_health_score' => round($avgMood / 10, 2),
            'energy_depletion_score' => round((11 - $avgEnergy) / 10, 2),
            'total_participants' => fake()->numberBetween(15, 100),
            'confidence_level' => round(fake()->randomFloat(2, 0.5, 1.0), 2),
            'trend_direction' => fake()->randomElement(['improving', 'stable', 'declining']),
        ];
    }

    public function highBurnoutRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'burnout_risk_level' => round(fake()->randomFloat(2, 0.6, 0.9), 2),
            'overall_wellbeing_score' => round(fake()->randomFloat(2, 3.0, 5.0), 2),
        ]);
    }

    public function healthy(): static
    {
        return $this->state(fn (array $attributes) => [
            'burnout_risk_level' => round(fake()->randomFloat(2, 0.1, 0.3), 2),
            'overall_wellbeing_score' => round(fake()->randomFloat(2, 7.0, 9.0), 2),
            'trend_direction' => 'improving',
        ]);
    }
}
