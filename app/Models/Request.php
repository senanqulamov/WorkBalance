<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Request extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'buyer_id',
        'title',
        'description',
        'deadline',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the buyer (user) that owns the request.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Get the items for this request.
     */
    public function items(): HasMany
    {
        return $this->hasMany(RequestItem::class);
    }

    /**
     * Get the quotes for this request.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Get the supplier invitations for this request.
     */
    public function supplierInvitations(): HasMany
    {
        return $this->hasMany(SupplierInvitation::class);
    }

    /**
     * Get the workflow events for this request.
     */
    public function workflowEvents()
    {
        return $this->morphMany(WorkflowEvent::class, 'eventable');
    }
}
