<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'department',
        'team',
        'metric_date',
        'avg_stress_level',
        'avg_energy_level',
        'avg_mood_level',
        'total_check_ins',
        'unique_users',
        'risk_signals',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'avg_stress_level' => 'decimal:2',
        'avg_energy_level' => 'decimal:2',
        'avg_mood_level' => 'decimal:2',
        'total_check_ins' => 'integer',
        'unique_users' => 'integer',
        'risk_signals' => 'array',
    ];

    /**
     * Get overall wellness score for this metric
     */
    public function getWellnessScoreAttribute(): float
    {
        if (!$this->avg_stress_level || !$this->avg_energy_level || !$this->avg_mood_level) {
            return 0;
        }

        $inverseStress = 11 - $this->avg_stress_level;
        return round(($inverseStress + $this->avg_energy_level + $this->avg_mood_level) / 3, 2);
    }

    /**
     * Check if minimum group size is met (privacy requirement)
     */
    public function meetsMinimumGroupSize(): bool
    {
        return $this->unique_users >= 10;
    }
}
