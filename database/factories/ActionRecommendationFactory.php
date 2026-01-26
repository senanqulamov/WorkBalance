<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActionRecommendationFactory extends Factory
{
    public function definition(): array
    {
        $types = ['workload_review', 'work_rhythm', 'communication', 'positive_pattern'];
        $priorities = ['high', 'medium', 'low', 'positive'];

        $type = fake()->randomElement($types);
        $priority = fake()->randomElement($priorities);

        return [
            'recommendation_type' => $type,
            'priority' => $priority,
            'target_scope' => fake()->randomElement(['organization', 'department', 'team']),
            'title' => $this->generateTitle($type, $priority),
            'description' => fake()->paragraph(),
            'suggested_actions' => $this->generateActions($type),
            'evidence_summary' => sprintf(
                'Based on aggregated check-in data from %d employees over %d days. Signal strength: %d%%',
                fake()->numberBetween(10, 50),
                fake()->numberBetween(7, 30),
                fake()->numberBetween(60, 95)
            ),
            'department_code' => Department::inRandomOrder()->first()?->code,
            'generated_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'acknowledged_at' => fake()->optional(0.4)->dateTimeBetween('-20 days', 'now'),
            'implemented_at' => fake()->optional(0.2)->dateTimeBetween('-10 days', 'now'),
        ];
    }

    protected function generateTitle(string $type, string $priority): string
    {
        $titles = [
            'workload_review' => 'Review Workload Distribution',
            'work_rhythm' => 'Support Work-Life Balance',
            'communication' => 'Strengthen Team Dynamics',
            'positive_pattern' => 'Strong Well-Being Patterns Detected',
        ];

        return $titles[$type] ?? 'Organization Improvement Suggestion';
    }

    protected function generateActions(string $type): array
    {
        $actions = [
            'workload_review' => [
                'Review current project deadlines and consider extensions',
                'Audit workload distribution to identify bottlenecks',
                'Ensure managers are having regular supportive 1-on-1s',
                'Consider bringing in temporary support or redistributing work',
            ],
            'work_rhythm' => [
                'Review meeting schedules and consider no-meeting blocks',
                'Encourage use of breaks and time off',
                'Check for after-hours work patterns',
                'Assess whether deadlines allow for sustainable pacing',
            ],
            'communication' => [
                'Check in with team leads about any known tensions',
                'Consider facilitated team retrospectives',
                'Ensure role clarity and decision-making authority',
                'Create space for informal connection',
            ],
            'positive_pattern' => [
                'Document what\'s working well in this team',
                'Consider sharing practices with other departments',
                'Recognize leadership for creating sustainable conditions',
                'Maintain these patterns even during busy periods',
            ],
        ];

        return $actions[$type] ?? ['Review situation', 'Take appropriate action', 'Monitor outcomes'];
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'positive',
            'recommendation_type' => 'positive_pattern',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'acknowledged_at' => null,
            'implemented_at' => null,
        ]);
    }
}
