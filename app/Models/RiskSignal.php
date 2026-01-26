<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'signal_type',
        'severity',
        'department_code',
        'affected_group_size',
        'signal_strength',
        'description',
        'detected_at',
        'acknowledged_at',
        'resolved_at',
        'metadata',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'signal_strength' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    /**
     * Get severity color for UI
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'elevated' => 'orange',
            'moderate' => 'yellow',
            default => 'blue',
        };
    }

    /**
     * Get human-readable signal type
     */
    public function getSignalTypeLabelAttribute(): string
    {
        return match($this->signal_type) {
            'burnout_risk' => 'Burnout Risk',
            'financial_stress' => 'Financial Stress',
            'energy_depletion' => 'Energy Depletion',
            'relationship_strain' => 'Relationship Strain',
            'leadership_pressure' => 'Leadership Pressure',
            default => ucwords(str_replace('_', ' ', $this->signal_type)),
        };
    }

    /**
     * Check if signal is active
     */
    public function isActive(): bool
    {
        return $this->resolved_at === null;
    }
}
