<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tool_id',
        'duration_seconds',
        'completed',
        'used_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tool()
    {
        return $this->belongsTo(WellbeingTool::class, 'tool_id');
    }
}
