<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PathStep
 *
 * Individual step within a therapeutic path.
 * Contains validation, regulation, insight, and action components.
 */
class PathStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapeutic_path_id',
        'step_order',
        'step_type',
        'title',
        'content',
        'prompt',
        'validation_text',
        'regulation_exercise',
        'insight_text',
        'micro_action',
    ];

    protected $casts = [
        'step_order' => 'integer',
    ];

    /**
     * Get the therapeutic path this step belongs to.
     */
    public function path(): BelongsTo
    {
        return $this->belongsTo(TherapeuticPath::class, 'therapeutic_path_id');
    }
}
