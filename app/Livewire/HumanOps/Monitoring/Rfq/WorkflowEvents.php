<?php

namespace App\Livewire\HumanOps\Monitoring\Rfq;

use App\Models\Request;
use App\Models\WorkflowEvent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class WorkflowEvents extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $requestId = null;
    public ?Request $request = null;
    public int $quantity = 10;
    public array $filterEventTypes = [];
    public ?string $filterUser = null;
    public ?string $filterDateFrom = null;
    public ?string $filterDateTo = null;

    public array $availableEventTypes = [
        'status_changed'        => 'RFQ Status Changed',
        'supplier_invited'      => 'Supplier Invited',
        'quote_submitted'       => 'Quote Submitted',
        'quote_status_changed'  => 'Quote Status Changed',
        'quote_updated'         => 'Quote Updated',
        'rfq_updated'           => 'RFQ Updated',
        'sla_reminder'          => 'SLA Reminder',
        'assigned'              => 'Assigned',
        'comment_added'         => 'Comment Added',
        'document_uploaded'     => 'Document Uploaded',
        'quote_accepted'        => 'Quote Accepted',
        'quote_rejected'        => 'Quote Rejected',
    ];

    #[On('monitoring::load::workflow_events')]
    public function loadWorkflowEvents(int $rfq): void
    {
        $this->requestId = $rfq;
        $this->request = Request::with('buyer')->find($rfq);
        $this->resetFilters();
        $this->showModal = true;
    }

    public function resetFilters(): void
    {
        $this->filterEventTypes = [];
        $this->filterUser = null;
        $this->filterDateFrom = null;
        $this->filterDateTo = null;
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->requestId = null;
        $this->request = null;
        $this->resetFilters();
    }

    public function getWorkflowEventsProperty()
    {
        if (!$this->requestId) {
            // Return empty paginator instead of collection
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                $this->quantity
            );
        }

        $query = WorkflowEvent::query()
            ->where('eventable_type', Request::class)
            ->where('eventable_id', $this->requestId)
            ->with('user');

        // Apply filters
        if (!empty($this->filterEventTypes)) {
            $query->whereIn('event_type', $this->filterEventTypes);
        }

        if ($this->filterUser) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->filterUser . '%');
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('occurred_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('occurred_at', '<=', $this->filterDateTo);
        }

        return $query->orderBy('occurred_at', 'desc')
            ->paginate($this->quantity);
    }

    public function getEventIcon(string $eventType): string
    {
        return match ($eventType) {
            'status_changed' => 'arrow-path',
            'supplier_invited' => 'user-plus',
            'quote_submitted' => 'document-check',
            'quote_status_changed' => 'arrow-path-rounded-square',
            'quote_updated' => 'pencil-square',
            'rfq_updated' => 'document-text',
            'sla_reminder' => 'bell-alert',
            'assigned' => 'user-circle',
            'comment_added' => 'chat-bubble-left-right',
            'document_uploaded' => 'paper-clip',
            'quote_accepted' => 'check-circle',
            'quote_rejected' => 'x-circle',
            default => 'bell',
        };
    }

    public function getEventColor(string $eventType): string
    {
        return match ($eventType) {
            'status_changed' => 'blue',
            'supplier_invited' => 'green',
            'quote_submitted' => 'purple',
            'quote_status_changed' => 'indigo',
            'quote_updated' => 'cyan',
            'rfq_updated' => 'teal',
            'sla_reminder' => 'amber',
            'assigned' => 'indigo',
            'comment_added' => 'gray',
            'document_uploaded' => 'slate',
            'quote_accepted' => 'emerald',
            'quote_rejected' => 'red',
            default => 'gray',
        };
    }

    public function render(): View
    {
        return view('livewire.humanops.monitoring.rfq.workflow-events');
    }
}
