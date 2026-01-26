<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Order statuses
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'user_id',
        'seller_id',
        'total',
        'status',
        'notes',
        'seller_notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
            if (empty($order->status)) {
                $order->status = self::STATUS_PENDING;
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-'.strtoupper(uniqid());
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['quantity', 'unit_price', 'subtotal', 'market_id'])
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the buyer (alias for user)
     */
    public function buyer()
    {
        return $this->user();
    }

    /**
     * Get the seller(s) from the markets in the order items
     * Returns the first seller if multiple markets
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get all markets involved in this order
     */
    public function markets()
    {
        return $this->belongsToMany(Market::class, 'order_items')
            ->distinct();
    }

    /**
     * Get items grouped by market
     */
    public function itemsByMarket()
    {
        return $this->items()
            ->with(['product', 'market'])
            ->get()
            ->groupBy('market_id');
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Accept the order
     */
    public function accept(?string $notes = null): void
    {
        $this->status = self::STATUS_ACCEPTED;
        if ($notes) {
            $this->seller_notes = $notes;
        }
        $this->save();
    }

    /**
     * Reject the order
     */
    public function reject(?string $notes = null): void
    {
        $this->status = self::STATUS_REJECTED;
        if ($notes) {
            $this->seller_notes = $notes;
        }
        $this->save();
    }

    public function recalcTotal(): void
    {
        $total = $this->items()->sum('subtotal');
        if ((float) $this->total !== (float) $total) {
            $this->total = $total;
            $this->saveQuietly();
        }
    }
}
