<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Organization
 *
 * Represents a company using WorkBalance.
 * Top-level entity for HumanOps Intelligence.
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'industry',
        'size',
        'primary_contact_email',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    /**
     * Get the teams in this organization.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the employees in this organization.
     */
    public function employees()
    {
        return $this->hasManyThrough(User::class, Team::class);
    }
}
