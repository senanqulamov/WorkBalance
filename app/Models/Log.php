<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'action',
        'model',
        'model_id',
        'message',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include logs of a given type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include logs for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include recent logs.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to only include logs for a specific model.
     */
    public function scopeForModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope a query to only include logs with a specific action.
     */
    public function scopeWithAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include logs within a date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope a query to only include error logs.
     */
    public function scopeErrors($query)
    {
        return $query->where('type', 'error');
    }

    /**
     * Scope a query to only include CRUD operations.
     */
    public function scopeCrudOperations($query)
    {
        return $query->whereIn('type', ['create', 'update', 'delete']);
    }
}
