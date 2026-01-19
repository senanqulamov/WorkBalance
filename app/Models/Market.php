<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'image_path',
    ];

    // The seller/owner of this market
    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias for seller
    public function owner()
    {
        return $this->seller();
    }

    // Direct order items for this market
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Get all orders that include products from this market
    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderItem::class, 'market_id', 'id', 'id', 'order_id')
            ->distinct();
    }

    // Direct products belonging to this market
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Worker users assigned to this market.
     */
    public function workers()
    {
        return $this->belongsToMany(User::class, 'market_users')
            ->withTimestamps();
    }
}
