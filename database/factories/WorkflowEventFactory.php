<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Models\Request;
use App\Models\User;
use App\Models\WorkflowEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkflowEvent>
 */
class WorkflowEventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkflowEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventTypes = ['status_changed', 'assigned', 'sla_reminder', 'comment_added', 'document_uploaded'];
        $eventType = $this->faker->randomElement($eventTypes);

        $fromState = null;
        $toState = null;

        if ($eventType === 'status_changed') {
            $statuses = [
                RequestStatus::DRAFT->value,
                RequestStatus::OPEN->value,
                RequestStatus::CLOSED->value,
                RequestStatus::AWARDED->value,
                RequestStatus::CANCELLED->value,
            ];
            $fromState = $this->faker->randomElement($statuses);
            $toState = $this->faker->randomElement(array_diff($statuses, [$fromState]));
        }

        return [
            'eventable_type' => null,
            'eventable_id' => null,
            'user_id' => null, // Must be set explicitly; do not auto-create users
            'event_type' => $eventType,
            'from_state' => $fromState,
            'to_state' => $toState,
            'description' => $this->faker->sentence(),
            'metadata' => $this->generateMetadata($eventType),
            'occurred_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Generate metadata based on event type
     *
     * @param string $eventType
     * @return array
     */
    private function generateMetadata(string $eventType): array
    {
        switch ($eventType) {
            case 'status_changed':
                return [
                    'reason' => $this->faker->optional(0.7)->sentence(),
                ];
            case 'assigned':
                return [
                    'previous_assignee_id' => $this->faker->optional(0.5)->numberBetween(1, 10),
                    'new_assignee_id' => $this->faker->numberBetween(1, 10),
                ];
            case 'sla_reminder':
                return [
                    'days_remaining' => $this->faker->numberBetween(1, 7),
                    'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
                ];
            case 'comment_added':
                return [
                    'comment_id' => $this->faker->numberBetween(1, 100),
                    'comment_text' => $this->faker->paragraph(),
                ];
            case 'document_uploaded':
                return [
                    'document_id' => $this->faker->numberBetween(1, 100),
                    'document_name' => $this->faker->word() . '.' . $this->faker->randomElement(['pdf', 'docx', 'xlsx']),
                    'document_size' => $this->faker->numberBetween(1000, 10000000),
                ];
            default:
                return [];
        }
    }

    /**
     * Create a status change event
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function statusChange(): Factory
    {
        return $this->state(function (array $attributes) {
            $statuses = [
                RequestStatus::DRAFT->value,
                RequestStatus::OPEN->value,
                RequestStatus::CLOSED->value,
                RequestStatus::AWARDED->value,
                RequestStatus::CANCELLED->value,
            ];
            $fromState = $this->faker->randomElement($statuses);
            $toState = $this->faker->randomElement(array_diff($statuses, [$fromState]));

            return [
                'event_type' => 'status_changed',
                'from_state' => $fromState,
                'to_state' => $toState,
                'description' => "Status changed from {$fromState} to {$toState}",
                'metadata' => [
                    'reason' => $this->faker->optional(0.7)->sentence(),
                ],
            ];
        });
    }

    /**
     * Create an assignment event
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function assignment(): Factory
    {
        return $this->state(function (array $attributes) {
            $previousAssigneeId = $this->faker->optional(0.5)->numberBetween(1, 10);
            $newAssigneeId = $this->faker->numberBetween(1, 10);

            return [
                'event_type' => 'assigned',
                'from_state' => $previousAssigneeId ? "user:{$previousAssigneeId}" : null,
                'to_state' => "user:{$newAssigneeId}",
                'description' => $previousAssigneeId
                    ? "Reassigned from User #{$previousAssigneeId} to User #{$newAssigneeId}"
                    : "Assigned to User #{$newAssigneeId}",
                'metadata' => [
                    'previous_assignee_id' => $previousAssigneeId,
                    'new_assignee_id' => $newAssigneeId,
                ],
            ];
        });
    }
}
