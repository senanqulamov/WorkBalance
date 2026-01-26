<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EmotionalCheckIn (formerly RequestItem)
 *
 * Captures an employee's emotional state at a point in time.
 * Includes mood, energy, and optional private notes.
 *
 * PRIVACY: Never accessible to employers. Aggregated anonymously only.
 */
class EmotionalCheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'wellbeing_cycle_id',
        'employee_id',
        'mood_level',
        'energy_level',
        'stress_level',
        'private_note',
        'checked_in_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'mood_level' => 'integer',
        'energy_level' => 'integer',
        'stress_level' => 'integer',
    ];

    /**
     * Get the wellbeing cycle this check-in belongs to.
     */
    public function cycle(): BelongsTo
    {
        return $this->belongsTo(WellbeingCycle::class, 'wellbeing_cycle_id');
    }

    /**
     * Get the employee who made this check-in.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
