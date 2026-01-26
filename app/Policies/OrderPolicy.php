<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_orders');
    }

    /**
     * Determine if the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Buyer can view their own orders
        if ($order->user_id === $user->id) {
            return true;
        }

        // Seller can view orders that contain products from their markets
        if ($user->isSeller()) {
            $hasSellerMarkets = $order->items()
                ->whereHas('market', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->exists();

            if ($hasSellerMarkets) {
                return true;
            }

            // Or if they're the designated seller (for legacy orders)
            if ($order->seller_id === $user->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_orders');
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Seller can update if it's their order (for accept/reject)
        if ($user->isSeller() && $order->seller_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin() ||
               ($user->hasPermission('delete_orders') && $order->user_id === $user->id);
    }

    /**
     * Determine if the seller can accept/reject the order.
     */
    public function manageStatus(User $user, Order $order): bool
    {
        return $user->isSeller() && $order->seller_id === $user->id;
    }
}
