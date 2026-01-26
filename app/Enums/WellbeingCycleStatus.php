<?php

namespace App\Enums;

/**
 * WellbeingCycleStatus
 *
 * Status of an employee's wellbeing journey cycle.
 */
enum WellbeingCycleStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case PAUSED = 'paused';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => __('Active'),
            self::COMPLETED => __('Completed'),
            self::PAUSED => __('Paused'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'blue',
            self::COMPLETED => 'green',
            self::PAUSED => 'gray',
        };
    }

    public function isFinal(): bool
    {
        return $this === self::COMPLETED;
    }
}
