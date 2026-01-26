<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationHealthIndex extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'overall_wellbeing_score',
        'burnout_risk_level',
        'financial_stress_level',
        'relationship_health_score',
        'energy_depletion_score',
        'total_participants',
        'confidence_level',
        'trend_direction',
    ];

    protected $casts = [
        'date' => 'date',
        'overall_wellbeing_score' => 'decimal:2',
        'burnout_risk_level' => 'decimal:2',
        'financial_stress_level' => 'decimal:2',
        'relationship_health_score' => 'decimal:2',
        'energy_depletion_score' => 'decimal:2',
        'confidence_level' => 'decimal:2',
    ];

    /**
     * Get risk level as human-readable string
     */
    public function getBurnoutRiskLabelAttribute(): string
    {
        if ($this->burnout_risk_level < 0.3) return 'Low';
        if ($this->burnout_risk_level < 0.6) return 'Moderate';
        return 'Elevated';
    }

    /**
     * Get trend as human-readable string
     */
    public function getTrendLabelAttribute(): string
    {
        return match($this->trend_direction) {
            'improving' => 'Improving',
            'declining' => 'Declining',
            default => 'Stable',
        };
    }
}
