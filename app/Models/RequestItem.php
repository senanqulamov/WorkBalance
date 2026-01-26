<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'request_id',
        'product_name',
        'quantity',
        'specifications',
    ];

    /**
     * Get the request that owns this item.
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * Get the product related to this request item (if product_id is set).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the total price for this request item (product price * quantity).
     */
    public function getTotalPriceAttribute(): float
    {
        if ($this->product && $this->product->price !== null) {
            return $this->product->price * $this->quantity;
        }
        return 0;
    }
}
