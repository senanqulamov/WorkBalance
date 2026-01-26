<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AggregatedWellbeingSignal extends Model
{
    protected $fillable = [
        'department_id',
        'period',
        'period_start',
        'period_end',
        'avg_stress',
        'avg_energy',
        'mood_index',
        'data_confidence',
        'participant_count',
        'calculated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'avg_stress' => 'decimal:2',
        'avg_energy' => 'decimal:2',
        'mood_index' => 'decimal:2',
        'data_confidence' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
