<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Team (formerly Market)
 *
 * Represents a team or department within an organization.
 * Used for aggregated wellbeing metrics in HumanOps.
 *
 * PRIVACY: Team metrics enforce minimum cohort size (≥5).
 */
class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization this team belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the manager of this team.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the employees in this team.
     */
    public function employees()
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    /**
     * Get aggregated metrics for this team.
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(TeamMetric::class);
    }
}
