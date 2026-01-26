<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'insight_type',
        'title',
        'description',
        'insight_data',
        'generated_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'insight_data' => 'array',
        'generated_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if insight is new (not acknowledged)
     */
    public function isNew(): bool
    {
        return $this->acknowledged_at === null;
    }

    /**
     * Mark insight as acknowledged
     */
    public function acknowledge(): void
    {
        $this->update(['acknowledged_at' => now()]);
    }
}
