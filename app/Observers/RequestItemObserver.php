<?php

namespace App\Observers;

use App\Events\RfqUpdated;
use App\Models\RequestItem;
use Illuminate\Support\Facades\Auth;

class RequestItemObserver
{
    /**
     * Handle the RequestItem "created" event.
     */
    public function created(RequestItem $item): void
    {
        try {
            $request = $item->request;
            if ($request) {
                $changes = [
                    'items' => [
                        'old' => 'N/A',
                        'new' => "Added item: {$item->product_name} (qty: {$item->quantity})",
                    ],
                ];
                event(new RfqUpdated($request, $changes, Auth::user()));
            }
        } catch (\Throwable $e) {
            \Log::error('RequestItemObserver: Failed to fire RfqUpdated event on create: ' . $e->getMessage());
        }
    }

    /**
     * Handle the RequestItem "updated" event.
     */
    public function updated(RequestItem $item): void
    {
        $changes = [];
        $dirty = $item->getDirty();
        $original = $item->getOriginal();

        if (empty($dirty)) {
            return;
        }

        // Track item changes
        $trackableFields = ['product_name', 'quantity', 'unit', 'specifications'];
        foreach ($trackableFields as $field) {
            if (isset($dirty[$field]) && isset($original[$field])) {
                $changes[$field] = [
                    'old' => $original[$field],
                    'new' => $dirty[$field],
                ];
            }
        }

        if (!empty($changes)) {
            try {
                $request = $item->request;
                if ($request) {
                    $itemChanges = [
                        'items' => [
                            'old' => json_encode($original),
                            'new' => "Updated item: {$item->product_name}",
                        ],
                    ];
                    event(new RfqUpdated($request, $itemChanges, Auth::user()));
                }
            } catch (\Throwable $e) {
                \Log::error('RequestItemObserver: Failed to fire RfqUpdated event: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the RequestItem "deleted" event.
     */
    public function deleted(RequestItem $item): void
    {
        try {
            $request = $item->request;
            if ($request) {
                $changes = [
                    'items' => [
                        'old' => "{$item->product_name} (qty: {$item->quantity})",
                        'new' => 'Removed',
                    ],
                ];
                event(new RfqUpdated($request, $changes, Auth::user()));
            }
        } catch (\Throwable $e) {
            \Log::error('RequestItemObserver: Failed to fire RfqUpdated event on delete: ' . $e->getMessage());
        }
    }
}
