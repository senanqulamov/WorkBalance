<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * WellbeingCycle (formerly Request/RFQ)
 *
 * Represents an employee's journey through emotional check-ins
 * and therapeutic interventions over a period of time.
 *
 * PRIVACY: Individual cycle data never exposed to employers.
 * Only aggregated, anonymized metrics flow to HumanOps.
 */
class WellbeingCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'started_at',
        'completed_at',
        'status',
        'cycle_type',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the employee (user) that owns this cycle.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the emotional check-ins for this cycle.
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(EmotionalCheckIn::class);
    }

    /**
     * Get the therapeutic sessions for this cycle.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(TherapeuticSession::class);
    }

    /**
     * Get the human events for this cycle.
     */
    public function humanEvents()
    {
        return $this->morphMany(HumanEvent::class, 'eventable');
    }
}
