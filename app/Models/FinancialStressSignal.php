<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialStressSignal extends Model
{
    protected $fillable = [
        'department_id',
        'stress_level',
        'trend_direction',
        'description',
        'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
