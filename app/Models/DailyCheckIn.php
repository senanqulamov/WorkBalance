<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCheckIn extends Model
{
    protected $fillable = [
        'user_id',
        'stress_level',
        'stress_value',
        'energy_level',
        'energy_value',
        'mood_state',
        'optional_note',
        'check_in_date',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'stress_value' => 'integer',
        'energy_value' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
