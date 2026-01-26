<?php

namespace App\Enums;

/**
 * BurnoutRiskLevel
 *
 * Aggregated burnout risk assessment for teams (never individuals).
 */
enum BurnoutRiskLevel: string
{
    case LOW = 'low';
    case MODERATE = 'moderate';
    case ELEVATED = 'elevated';
    case HIGH = 'high';

    public function label(): string
    {
        return match($this) {
            self::LOW => __('Low Risk'),
            self::MODERATE => __('Moderate Risk'),
            self::ELEVATED => __('Elevated Risk'),
            self::HIGH => __('High Risk'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::LOW => 'green',
            self::MODERATE => 'yellow',
            self::ELEVATED => 'amber',
            self::HIGH => 'orange',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::LOW => __('Team wellbeing is stable. Continue current support.'),
            self::MODERATE => __('Some pressure signals detected. Monitor trends.'),
            self::ELEVATED => __('Team showing consistent stress. Consider interventions.'),
            self::HIGH => __('Urgent attention needed. Support measures recommended.'),
        };
    }

    public function requiresAction(): bool
    {
        return in_array($this, [self::ELEVATED, self::HIGH]);
    }
}
