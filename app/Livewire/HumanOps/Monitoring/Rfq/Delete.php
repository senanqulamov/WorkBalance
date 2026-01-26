<?php

namespace App\Livewire\HumanOps\Monitoring\Rfq;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert, WithLogging;

    public Request $rfq;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" wire:click="{{__('confirm')}}" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        // Ensure the buyer owns this RFQ
        if ($this->rfq->buyer_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            $this->error(__('Only the owner of this RFQ or Admin can delete it.'));
            return;
        }

        $this->question()
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        // Ensure the buyer owns this RFQ
        if ($this->rfq->buyer_id !== Auth::id() && !Auth::user()->hasRole('Admin')) {
            $this->error(__('Only the owner of this RFQ or Admin can delete it.'));
            return;
        }

        $requestData = [
            'title' => $this->rfq->title,
            'description' => $this->rfq->description,
            'status' => $this->rfq->status,
        ];
        $requestId = $this->rfq->id;

        $this->rfq->delete();
        $this->logDelete(Request::class, $requestId, $requestData);

        $this->dispatch('deleted');

        $this->success(__('RFQ deleted successfully.'));
    }
}
