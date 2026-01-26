<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'eventable_type',
        'eventable_id',
        'user_id',
        'event_type',
        'from_state',
        'to_state',
        'description',
        'occurred_at',
        'metadata',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the parent eventable model (Request, Quote, etc.).
     */
    public function eventable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who triggered the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
