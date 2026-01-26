<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TeamMetric
 *
 * Aggregated wellbeing metrics for a team over time.
 * Powers HumanOps Intelligence dashboards.
 *
 * PRIVACY: Requires minimum cohort size (≥5) for calculation.
 */
class TeamMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'metric_date',
        'cohort_size',
        'stress_trend',
        'engagement_rate',
        'burnout_risk_level',
        'check_in_participation',
        'paths_completed',
        'average_intensity_shift',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'cohort_size' => 'integer',
        'check_in_participation' => 'decimal:2',
        'average_intensity_shift' => 'decimal:2',
    ];

    /**
     * Get the team this metric belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Check if cohort size meets minimum privacy threshold.
     */
    public function meetsPrivacyThreshold(): bool
    {
        return $this->cohort_size >= 5;
    }
}
