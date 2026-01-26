<?php

namespace App\Livewire\Sla;

use App\Jobs\CheckRfqDeadlines;
use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Bus;
use Livewire\Component;

/**
 * @method void dispatchBrowserEvent(string $event, array $data = [])
 */
class Index extends Component
{
    use Alert, WithLogging;

    public array $overview = [];

    public function mount(): void
    {
        $this->loadOverview();
    }

    public function loadOverview(): void
    {
        $this->overview = [
            'open' => Request::where('status', 'open')->count(),
            'due_7' => Request::where('status', 'open')->whereBetween('deadline', [now(), now()->addDays(7)])->count(),
            'due_3' => Request::where('status', 'open')->whereBetween('deadline', [now(), now()->addDays(3)])->count(),
            'due_1' => Request::where('status', 'open')->whereBetween('deadline', [now(), now()->addDay()])->count(),
            'overdue' => Request::where('status', 'open')->where('deadline', '<', now())->count(),
        ];
    }

    public function dispatchReminders(): void
    {
        Bus::dispatch(new CheckRfqDeadlines());
        $this->success(__('SLA reminders job dispatched.'));
    }

    public function render(): View
    {
        return view('livewire.sla.index');
    }
}
