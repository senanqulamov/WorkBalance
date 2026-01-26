<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stress_level',
        'energy_level',
        'mood_level',
        'notes',
        'check_in_date',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'stress_level' => 'integer',
        'energy_level' => 'integer',
        'mood_level' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the overall wellness score (inverse of stress, plus energy and mood)
     */
    public function getWellnessScoreAttribute(): float
    {
        $inverseStress = 11 - $this->stress_level; // 10 becomes 1, 1 becomes 10
        return round(($inverseStress + $this->energy_level + $this->mood_level) / 3, 2);
    }
}
