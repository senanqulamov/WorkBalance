<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ActivitySignal (formerly Log)
 *
 * Anonymized user activity for system health and patterns.
 * Used for HumanOps insights and platform improvements.
 *
 * PRIVACY: No emotional content, only interaction patterns.
 */
class ActivitySignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'action_type',
        'description',
        'context',
        'occurred_at',
        'metadata',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the team this signal belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
