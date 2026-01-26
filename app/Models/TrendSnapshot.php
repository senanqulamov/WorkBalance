<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrendSnapshot extends Model
{
    protected $fillable = [
        'scope',
        'department_id',
        'metric',
        'value',
        'period',
        'period_start',
    ];

    protected $casts = [
        'period_start' => 'date',
        'value' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
