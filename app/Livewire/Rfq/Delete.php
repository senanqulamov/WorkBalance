<?php

namespace App\Livewire\Rfq;

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
            <x-button.circle icon="trash" color="red" wire:click="confirm" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('delete_rfqs')) {
            $this->error('You do not have permission to delete RFQs.');
            return;
        }

        $this->question()
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('delete_rfqs')) {
            $this->error('You do not have permission to delete RFQs.');
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

        $this->success();
    }
}
