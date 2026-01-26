<?php

namespace App\Observers;

use App\Events\QuoteStatusChanged;
use App\Events\QuoteUpdated;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;

class QuoteObserver
{
    /**
     * Handle the Quote "updated" event.
     */
    public function updated(Quote $quote): void
    {
        // Get the changes that were made
        $changes = [];
        $dirty = $quote->getDirty();
        $original = $quote->getOriginal();

        // Skip if no changes
        if (empty($dirty)) {
            return;
        }

        // Check if status changed - fire specific event
        if (isset($dirty['status']) && isset($original['status'])) {
            $oldStatus = $original['status'];
            $newStatus = $dirty['status'];

            try {
                event(new QuoteStatusChanged($quote, $oldStatus, $newStatus, Auth::user()));
            } catch (\Throwable $e) {
                \Log::error('QuoteObserver: Failed to fire QuoteStatusChanged event: ' . $e->getMessage());
            }
        }

        // Track other field changes
        $trackableFields = [
            'unit_price',
            'total_price',
            'total_amount',
            'currency',
            'valid_until',
            'notes',
            'terms_conditions',
            'delivery_time',
            'warranty_period'
        ];

        foreach ($trackableFields as $field) {
            if (isset($dirty[$field]) && isset($original[$field])) {
                $changes[$field] = [
                    'old' => $original[$field],
                    'new' => $dirty[$field],
                ];
            }
        }

        // Fire QuoteUpdated event if there are trackable changes
        if (!empty($changes)) {
            try {
                event(new QuoteUpdated($quote, $changes, Auth::user()));
            } catch (\Throwable $e) {
                \Log::error('QuoteObserver: Failed to fire QuoteUpdated event: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Quote "created" event.
     */
    public function created(Quote $quote): void
    {
        // Quote submission is already handled by QuoteSubmitted event
        // No need to duplicate here
    }
}
