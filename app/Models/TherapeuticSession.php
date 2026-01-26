<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TherapeuticSession (formerly Quote)
 *
 * Represents a guided therapeutic intervention path
 * chosen by an employee in response to a specific situation.
 *
 * PRIVACY: Session content remains private to the employee.
 */
class TherapeuticSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'wellbeing_cycle_id',
        'employee_id',
        'therapeutic_path_id',
        'situation_type',
        'started_at',
        'completed_at',
        'status',
        'intensity_before',
        'intensity_after',
        'reflection_note',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'intensity_before' => 'integer',
        'intensity_after' => 'integer',
    ];

    /**
     * Get the wellbeing cycle this session belongs to.
     */
    public function cycle(): BelongsTo
    {
        return $this->belongsTo(WellbeingCycle::class, 'wellbeing_cycle_id');
    }

    /**
     * Get the employee who participated in this session.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the therapeutic path used in this session.
     */
    public function path(): BelongsTo
    {
        return $this->belongsTo(TherapeuticPath::class, 'therapeutic_path_id');
    }

    /**
     * Get the reflections for this session.
     */
    public function reflections(): HasMany
    {
        return $this->hasMany(Reflection::class);
    }
}
