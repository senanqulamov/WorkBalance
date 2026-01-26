<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reflection_date',
        'content',
        'emotional_tags',
        'is_private',
    ];

    protected $casts = [
        'reflection_date' => 'date',
        'emotional_tags' => 'array',
        'is_private' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
