<?php

namespace App\Observers;

use App\Events\RequestStatusChanged;
use App\Events\RfqUpdated;
use App\Models\Request;
use App\Enums\RequestStatus;
use Illuminate\Support\Facades\Auth;

class RequestObserver
{
    /**
     * Handle the Request "updated" event.
     */
    public function updated(Request $request): void
    {
        // Get the changes that were made
        $changes = [];
        $dirty = $request->getDirty();
        $original = $request->getOriginal();

        // Skip if no changes
        if (empty($dirty)) {
            return;
        }

        // Check if status changed - fire specific event
        if (isset($dirty['status']) && isset($original['status'])) {
            $oldStatus = $original['status'];
            $newStatus = $dirty['status'];

            try {
                $oldStatusEnum = $oldStatus ? RequestStatus::tryFrom($oldStatus) : null;
                $newStatusEnum = RequestStatus::tryFrom($newStatus);

                if ($newStatusEnum) {
                    event(new RequestStatusChanged($request, $oldStatusEnum, $newStatusEnum, Auth::user()));
                }
            } catch (\Throwable $e) {
                \Log::error('RequestObserver: Failed to fire RequestStatusChanged event: ' . $e->getMessage());
            }
        }

        // Track other field changes
        $trackableFields = ['title', 'description', 'deadline', 'budget', 'currency', 'priority', 'notes'];
        foreach ($trackableFields as $field) {
            if (isset($dirty[$field]) && isset($original[$field])) {
                $changes[$field] = [
                    'old' => $original[$field],
                    'new' => $dirty[$field],
                ];
            }
        }

        // Fire RfqUpdated event if there are trackable changes
        if (!empty($changes)) {
            try {
                event(new RfqUpdated($request, $changes, Auth::user()));
            } catch (\Throwable $e) {
                \Log::error('RequestObserver: Failed to fire RfqUpdated event: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the Request "created" event.
     */
    public function created(Request $request): void
    {
        // Fire status changed event for initial creation
        try {
            $statusEnum = RequestStatus::tryFrom($request->status ?? 'draft');
            if ($statusEnum) {
                event(new RequestStatusChanged($request, null, $statusEnum, Auth::user()));
            }
        } catch (\Throwable $e) {
            \Log::error('RequestObserver: Failed to fire RequestStatusChanged event on create: ' . $e->getMessage());
        }
    }
}
