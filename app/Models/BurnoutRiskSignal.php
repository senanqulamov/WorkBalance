<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BurnoutRiskSignal extends Model
{
    protected $fillable = [
        'department_id',
        'risk_level',
        'trend_direction',
        'description',
        'signal_strength',
        'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
        'signal_strength' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
