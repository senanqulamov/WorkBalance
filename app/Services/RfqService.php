<?php

namespace App\Services;

use App\Enums\RequestStatus;
use App\Models\Request;
use App\Models\RequestItem;
use App\Models\User;
use Illuminate\Support\Collection;

class RfqService
{
    /**
     * Create a new RFQ (Request for Quote)
     *
     * @param array $data The RFQ data
     * @param User $buyer The user creating the RFQ
     * @return Request The created RFQ
     */
    public function createRfq(array $data, User $buyer): Request
    {
        $rfq = new Request();
        $rfq->buyer_id = $buyer->id;
        $rfq->title = $data['title'];
        $rfq->description = $data['description'] ?? null;
        $rfq->deadline = $data['deadline'];
        $rfq->status = RequestStatus::DRAFT->value;
        $rfq->save();

        // Create request items
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $requestItem = new RequestItem();
                $requestItem->request_id = $rfq->id;
                $requestItem->product_name = $item['product_name'];
                $requestItem->quantity = $item['quantity'];
                $requestItem->specifications = $item['specifications'] ?? null;
                $requestItem->save();
            }
        }

        return $rfq;
    }

    /**
     * Update an existing RFQ
     *
     * @param Request $rfq The RFQ to update
     * @param array $data The updated data
     * @return Request The updated RFQ
     */
    public function updateRfq(Request $rfq, array $data): Request
    {
        // Only allow updates if the RFQ is in a state that allows editing
        if (!RequestStatus::from($rfq->status)->allowsEditing()) {
            throw new \Exception("This RFQ cannot be edited in its current state.");
        }

        $rfq->title = $data['title'] ?? $rfq->title;
        $rfq->description = $data['description'] ?? $rfq->description;
        $rfq->deadline = $data['deadline'] ?? $rfq->deadline;
        $rfq->save();

        // Update request items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            // Get existing items
            $existingItems = $rfq->items;
            $existingItemIds = $existingItems->pluck('id')->toArray();
            $updatedItemIds = [];

            foreach ($data['items'] as $item) {
                if (isset($item['id']) && in_array($item['id'], $existingItemIds)) {
                    // Update existing item
                    $requestItem = RequestItem::find($item['id']);
                    $requestItem->product_name = $item['product_name'] ?? $requestItem->product_name;
                    $requestItem->quantity = $item['quantity'] ?? $requestItem->quantity;
                    $requestItem->specifications = $item['specifications'] ?? $requestItem->specifications;
                    $requestItem->save();
                    $updatedItemIds[] = $requestItem->id;
                } else {
                    // Create new item
                    $requestItem = new RequestItem();
                    $requestItem->request_id = $rfq->id;
                    $requestItem->product_name = $item['product_name'];
                    $requestItem->quantity = $item['quantity'];
                    $requestItem->specifications = $item['specifications'] ?? null;
                    $requestItem->save();
                    $updatedItemIds[] = $requestItem->id;
                }
            }

            // Delete items that were not updated or created
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                RequestItem::whereIn('id', $itemsToDelete)->delete();
            }
        }

        return $rfq->fresh();
    }

    /**
     * Change the status of an RFQ
     *
     * @param Request $rfq The RFQ to update
     * @param RequestStatus $newStatus The new status
     * @param User|null $user The user making the change
     * @return Request The updated RFQ
     */
    public function changeStatus(Request $rfq, RequestStatus $newStatus, ?User $user = null): Request
    {
        $oldStatus = RequestStatus::from($rfq->status);

        // Validate status transition
        $this->validateStatusTransition($oldStatus, $newStatus);

        // Update the status
        $rfq->status = $newStatus->value;
        $rfq->save();

        // Record the status change in workflow_events
        // This would be implemented in Task T4

        return $rfq->fresh();
    }

    /**
     * Validate that a status transition is allowed
     *
     * @param RequestStatus $oldStatus The current status
     * @param RequestStatus $newStatus The new status
     * @throws \Exception If the transition is not allowed
     */
    private function validateStatusTransition(RequestStatus $oldStatus, RequestStatus $newStatus): void
    {
        // Define allowed transitions
        $allowedTransitions = [
            RequestStatus::DRAFT->value => [RequestStatus::OPEN, RequestStatus::CANCELLED],
            RequestStatus::OPEN->value => [RequestStatus::CLOSED, RequestStatus::AWARDED, RequestStatus::CANCELLED],
            RequestStatus::CLOSED->value => [RequestStatus::AWARDED, RequestStatus::CANCELLED],
            // No transitions from AWARDED or CANCELLED (final states)
        ];

        // Check if the transition is allowed
        if ($oldStatus->isFinal() || !isset($allowedTransitions[$oldStatus->value]) || !in_array($newStatus, $allowedTransitions[$oldStatus->value])) {
            throw new \Exception("Status transition from {$oldStatus->label()} to {$newStatus->label()} is not allowed.");
        }
    }

    /**
     * Get RFQs for a specific user
     *
     * @param User $user The user
     * @param array $filters Optional filters
     * @return Collection The RFQs
     */
    public function getRfqsForUser(User $user, array $filters = []): Collection
    {
        $query = Request::query();

        // Filter by user role
        if ($user->hasRole('buyer')) {
            $query->where('buyer_id', $user->id);
        } elseif ($user->hasRole('supplier')) {
            // For suppliers, get RFQs where they have been invited
            $query->whereHas('supplierInvitations', function ($q) use ($user) {
                $q->where('supplier_id', $user->id);
            });
        }

        // Apply additional filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                  ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        // Order by deadline by default
        $query->orderBy('deadline', 'asc');

        return $query->get();
    }
}
