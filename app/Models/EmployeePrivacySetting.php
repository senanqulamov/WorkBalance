<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePrivacySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'allow_aggregation',
        'allow_trend_use',
        'last_updated',
    ];

    protected $casts = [
        'allow_aggregation' => 'boolean',
        'allow_trend_use' => 'boolean',
        'last_updated' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
