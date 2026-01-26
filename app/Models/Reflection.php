<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Reflection
 *
 * Employee's personal reflection after completing a therapeutic session.
 *
 * PRIVACY: Never accessible to employers. Purely for employee benefit.
 */
class Reflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapeutic_session_id',
        'employee_id',
        'what_changed',
        'intensity_shift',
        'key_insight',
        'next_action',
        'reflected_at',
    ];

    protected $casts = [
        'reflected_at' => 'datetime',
        'intensity_shift' => 'integer',
    ];

    /**
     * Get the session this reflection belongs to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(TherapeuticSession::class, 'therapeutic_session_id');
    }

    /**
     * Get the employee who wrote this reflection.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
