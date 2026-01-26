<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingTool extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'duration_seconds',
        'content_data',
        'is_active',
    ];

    protected $casts = [
        'content_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function usageLogs()
    {
        return $this->hasMany(ToolUsageLog::class, 'tool_id');
    }
}
