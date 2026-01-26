<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'is_enabled',
        'config',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Check if a feature is enabled
     */
    public static function isEnabled(string $key): bool
    {
        return static::where('key', $key)
            ->where('is_enabled', true)
            ->exists();
    }

    /**
     * Enable a feature flag
     */
    public function enable(): bool
    {
        return $this->update(['is_enabled' => true]);
    }

    /**
     * Disable a feature flag
     */
    public function disable(): bool
    {
        return $this->update(['is_enabled' => false]);
    }
}
