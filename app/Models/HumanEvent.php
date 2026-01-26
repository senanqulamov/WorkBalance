<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * HumanEvent (formerly WorkflowEvent)
 *
 * Tracks anonymized, time-based wellbeing signals and transitions.
 * Used for HumanOps aggregated insights.
 *
 * PRIVACY: Never contains PII or personal emotional content.
 */
class HumanEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'eventable_type',
        'eventable_id',
        'team_id',
        'event_type',
        'from_state',
        'to_state',
        'description',
        'occurred_at',
        'metadata',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the parent eventable model (WellbeingCycle, Team, etc.).
     */
    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the team this event belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
