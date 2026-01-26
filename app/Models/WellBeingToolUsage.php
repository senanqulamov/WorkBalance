<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellBeingToolUsage extends Model
{
    use HasFactory;

    // Schema table name (see unified migration)
    protected $table = 'tool_usage_logs';

    protected $fillable = [
        'user_id',
        'tool_id',
        'duration_seconds',
        'completed',
        'used_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'duration_seconds' => 'integer',
        'used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(WellbeingTool::class, 'tool_id');
    }
}
