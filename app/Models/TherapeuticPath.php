<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TherapeuticPath (formerly Product)
 *
 * A structured, therapist-designed intervention flow
 * for specific workplace emotional situations.
 *
 * Examples: Deadline Pressure, Burnout Risk, Conflict Resolution
 */
class TherapeuticPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'path_category_id',
        'name',
        'slug',
        'description',
        'situation_trigger',
        'steps_data',
        'estimated_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'steps_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category this path belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PathCategory::class, 'path_category_id');
    }

    /**
     * Get the path steps for this therapeutic path.
     */
    public function steps(): HasMany
    {
        return $this->hasMany(PathStep::class);
    }

    /**
     * Get sessions that used this path.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(TherapeuticSession::class);
    }
}
