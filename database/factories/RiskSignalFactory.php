<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class RiskSignalFactory extends Factory
{
    public function definition(): array
    {
        $signalTypes = ['burnout_risk', 'energy_depletion', 'relationship_strain', 'financial_stress', 'leadership_pressure'];
        $severities = ['low', 'moderate', 'elevated'];

        $type = fake()->randomElement($signalTypes);
        $severity = fake()->randomElement($severities);

        return [
            'signal_type' => $type,
            'severity' => $severity,
            'department_code' => Department::inRandomOrder()->first()?->code,
            'affected_group_size' => fake()->numberBetween(10, 50),
            'signal_strength' => round(fake()->randomFloat(2, 0.3, 0.9), 2),
            'description' => $this->generateDescription($type, $severity),
            'detected_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'acknowledged_at' => fake()->optional(0.3)->dateTimeBetween('-20 days', 'now'),
            'resolved_at' => fake()->optional(0.1)->dateTimeBetween('-10 days', 'now'),
        ];
    }

    protected function generateDescription(string $type, string $severity): string
    {
        $templates = [
            'burnout_risk' => [
                'low' => 'Mild burnout indicators detected. Team shows slightly elevated stress with reduced energy levels.',
                'moderate' => 'Moderate burnout risk identified. Team experiencing sustained high stress and low energy patterns.',
                'elevated' => 'Elevated burnout risk. Team shows critical combination of high stress and energy depletion requiring immediate attention.',
            ],
            'energy_depletion' => [
                'low' => 'Team energy levels slightly below optimal. May indicate minor fatigue or workload pressures.',
                'moderate' => 'Consistent energy depletion patterns detected. Team may be experiencing sustained fatigue.',
                'elevated' => 'Severe energy depletion. Team showing persistent low energy levels suggesting overwork or burnout.',
            ],
            'relationship_strain' => [
                'low' => 'Minor indicators of team morale challenges. Communication or collaboration may need attention.',
                'moderate' => 'Relationship or communication challenges detected. Team mood indicators suggest interpersonal strain.',
                'elevated' => 'Significant relationship strain indicators. Team dynamics may require facilitated intervention.',
            ],
        ];

        return $templates[$type][$severity] ?? 'Risk pattern detected requiring attention.';
    }

    public function elevated(): static
    {
        return $this->state(fn (array $attributes) => [
            'severity' => 'elevated',
            'signal_strength' => round(fake()->randomFloat(2, 0.7, 0.95), 2),
        ]);
    }

    public function unresolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'acknowledged_at' => null,
            'resolved_at' => null,
        ]);
    }
}
