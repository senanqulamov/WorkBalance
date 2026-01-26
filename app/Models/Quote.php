<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'supplier_id',
        'supplier_invitation_id',
        'unit_price',
        'total_price',
        'total_amount',
        'currency',
        'valid_until',
        'notes',
        'terms_conditions',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'submitted_at' => 'datetime',
        'total_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Get the request that this quote belongs to.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the supplier (user) that provided this quote.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get the supplier invitation this quote responds to.
     */
    public function supplierInvitation(): BelongsTo
    {
        return $this->belongsTo(SupplierInvitation::class, 'supplier_invitation_id');
    }

    /**
     * Get the quote items.
     */
    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    /**
     * Accessor: get the formatted total price as money.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        $value = $this->total_price ?? 0;

        return number_format((float) $value, 2, '.', ' ');
    }
}
