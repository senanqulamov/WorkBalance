<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalTrendsCache extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'period_start',
        'period_end',
        'avg_stress',
        'avg_energy',
        'mood_stability',
        'calculated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'calculated_at' => 'datetime',
        'avg_stress' => 'decimal:2',
        'avg_energy' => 'decimal:2',
        'mood_stability' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
