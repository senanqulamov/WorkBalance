<?php

namespace App\Enums;

/**
 * HumanEventType
 *
 * Types of anonymized wellbeing events tracked in HumanOps.
 */
enum HumanEventType: string
{
    case CHECK_IN_COMPLETED = 'check_in_completed';
    case PATH_STARTED = 'path_started';
    case PATH_COMPLETED = 'path_completed';
    case SESSION_COMPLETED = 'session_completed';
    case REFLECTION_ADDED = 'reflection_added';
    case STRESS_TREND_CHANGED = 'stress_trend_changed';
    case BURNOUT_THRESHOLD_CROSSED = 'burnout_threshold_crossed';
    case ENGAGEMENT_SHIFT = 'engagement_shift';
    case CYCLE_STARTED = 'cycle_started';
    case CYCLE_COMPLETED = 'cycle_completed';

    public function label(): string
    {
        return match($this) {
            self::CHECK_IN_COMPLETED => __('Check-in completed'),
            self::PATH_STARTED => __('Therapeutic path started'),
            self::PATH_COMPLETED => __('Therapeutic path completed'),
            self::SESSION_COMPLETED => __('Session completed'),
            self::REFLECTION_ADDED => __('Reflection added'),
            self::STRESS_TREND_CHANGED => __('Stress trend changed'),
            self::BURNOUT_THRESHOLD_CROSSED => __('Burnout threshold crossed'),
            self::ENGAGEMENT_SHIFT => __('Engagement shift detected'),
            self::CYCLE_STARTED => __('Wellbeing cycle started'),
            self::CYCLE_COMPLETED => __('Wellbeing cycle completed'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CHECK_IN_COMPLETED => 'blue',
            self::PATH_STARTED => 'purple',
            self::PATH_COMPLETED => 'green',
            self::SESSION_COMPLETED => 'green',
            self::REFLECTION_ADDED => 'indigo',
            self::STRESS_TREND_CHANGED => 'yellow',
            self::BURNOUT_THRESHOLD_CROSSED => 'amber',
            self::ENGAGEMENT_SHIFT => 'cyan',
            self::CYCLE_STARTED => 'blue',
            self::CYCLE_COMPLETED => 'green',
        };
    }

    /**
     * Check if this event type indicates a risk signal.
     */
    public function isRiskSignal(): bool
    {
        return in_array($this, [
            self::STRESS_TREND_CHANGED,
            self::BURNOUT_THRESHOLD_CROSSED,
        ]);
    }
}
