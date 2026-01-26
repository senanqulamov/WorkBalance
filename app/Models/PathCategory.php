<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PathCategory (formerly Category)
 *
 * Organizes therapeutic paths by emotional theme.
 * Examples: Stress Management, Conflict Resolution, Motivation
 */
class PathCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the therapeutic paths in this category.
     */
    public function paths(): HasMany
    {
        return $this->hasMany(TherapeuticPath::class, 'path_category_id');
    }
}
