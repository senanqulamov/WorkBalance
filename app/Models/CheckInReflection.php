<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckInReflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reflection_text',
        'related_check_in_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(DailyCheckIn::class, 'related_check_in_id');
    }
}
