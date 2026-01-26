<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'recommendation_type',
        'priority',
        'target_scope',
        'title',
        'description',
        'suggested_actions',
        'evidence_summary',
        'department_code',
        'generated_at',
        'acknowledged_at',
        'implemented_at',
    ];

    protected $casts = [
        'suggested_actions' => 'array',
        'generated_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'implemented_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'orange',
            'medium' => 'yellow',
            'positive' => 'green',
            default => 'blue',
        };
    }

    /**
     * Check if recommendation is pending
     */
    public function isPending(): bool
    {
        return $this->acknowledged_at === null;
    }
}
