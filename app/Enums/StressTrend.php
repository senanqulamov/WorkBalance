<?php

namespace App\Enums;

/**
 * StressTrend
 *
 * Aggregated stress direction for team cohorts.
 */
enum StressTrend: string
{
    case COOLING = 'cooling';
    case STEADY = 'steady';
    case RISING = 'rising';
    case VOLATILE = 'volatile';

    public function label(): string
    {
        return match($this) {
            self::COOLING => __('Cooling'),
            self::STEADY => __('Steady'),
            self::RISING => __('Rising'),
            self::VOLATILE => __('Volatile'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::COOLING => 'green',
            self::STEADY => 'blue',
            self::RISING => 'amber',
            self::VOLATILE => 'orange',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::COOLING => '↓',
            self::STEADY => '→',
            self::RISING => '↑',
            self::VOLATILE => '↕',
        };
    }
}
