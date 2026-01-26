<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RelationshipHealthSignal extends Model
{
    protected $fillable = [
        'department_id',
        'strain_level',
        'volatility',
        'description',
        'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
        'volatility' => 'decimal:2',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
