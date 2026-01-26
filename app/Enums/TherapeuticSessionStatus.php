<?php

namespace App\Enums;

/**
 * TherapeuticSessionStatus
 *
 * Status of a therapeutic intervention session.
 */
enum TherapeuticSessionStatus: string
{
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case PAUSED = 'paused';
    case ABANDONED = 'abandoned';

    public function label(): string
    {
        return match($this) {
            self::IN_PROGRESS => __('In Progress'),
            self::COMPLETED => __('Completed'),
            self::PAUSED => __('Paused'),
            self::ABANDONED => __('Abandoned'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::IN_PROGRESS => 'blue',
            self::COMPLETED => 'green',
            self::PAUSED => 'gray',
            self::ABANDONED => 'orange',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::COMPLETED, self::ABANDONED]);
    }
}
