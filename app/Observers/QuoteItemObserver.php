<?php

namespace App\Observers;

use App\Events\QuoteUpdated;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\Auth;

class QuoteItemObserver
{
    /**
     * Handle the QuoteItem "created" event.
     */
    public function created(QuoteItem $item): void
    {
        try {
            $quote = $item->quote;
            if ($quote && $quote->request) {
                $changes = [
                    'quote_items' => [
                        'old' => 'N/A',
                        'new' => "Added quote item (qty: {$item->quantity}, price: {$item->unit_price})",
                    ],
                ];
                event(new QuoteUpdated($quote, $changes, Auth::user()));
            }
        } catch (\Throwable $e) {
            \Log::error('QuoteItemObserver: Failed to fire QuoteUpdated event on create: ' . $e->getMessage());
        }
    }

    /**
     * Handle the QuoteItem "updated" event.
     */
    public function updated(QuoteItem $item): void
    {
        $changes = [];
        $dirty = $item->getDirty();
        $original = $item->getOriginal();

        if (empty($dirty)) {
            return;
        }

        // Track item changes
        $trackableFields = ['quantity', 'unit_price', 'subtotal', 'notes'];
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
                $quote = $item->quote;
                if ($quote && $quote->request) {
                    $itemChanges = [
                        'quote_items' => [
                            'old' => json_encode($original),
                            'new' => "Updated quote item",
                        ],
                    ];
                    event(new QuoteUpdated($quote, $itemChanges, Auth::user()));
                }
            } catch (\Throwable $e) {
                \Log::error('QuoteItemObserver: Failed to fire QuoteUpdated event: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the QuoteItem "deleted" event.
     */
    public function deleted(QuoteItem $item): void
    {
        try {
            $quote = $item->quote;
            if ($quote && $quote->request) {
                $changes = [
                    'quote_items' => [
                        'old' => "Item (qty: {$item->quantity}, price: {$item->unit_price})",
                        'new' => 'Removed',
                    ],
                ];
                event(new QuoteUpdated($quote, $changes, Auth::user()));
            }
        } catch (\Throwable $e) {
            \Log::error('QuoteItemObserver: Failed to fire QuoteUpdated event on delete: ' . $e->getMessage());
        }
    }
}
