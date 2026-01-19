<?php

namespace App\Livewire\Supplier\Rfq;

use App\Models\Quote;
use App\Models\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Request $request;
    public bool $canQuote = false;
    public ?Quote $supplierQuote = null;

    public function mount(Request $request): void
    {
        $this->request = $request->load(['buyer', 'items', 'quotes.supplier', 'quotes.items']);

        $user = Auth::user();

        // Find supplier's own quote
        $this->supplierQuote = Quote::where('request_id', $this->request->id)
                                    ->where('supplier_id', $user->id)
                                    ->first();

        // Check if supplier can submit a quote
        if ($user && $user->id !== $this->request->buyer_id) {
            // Check if RFQ is open and not past deadline
            $isOpen = $this->request->status === 'open' &&
                      (!$this->request->deadline || !$this->request->deadline->isPast());

            // Check if supplier hasn't already submitted a quote
            $hasQuoted = $this->supplierQuote !== null;

            $this->canQuote = $isOpen && !$hasQuoted;
        }
    }

    public function render(): View
    {
        return view('livewire.supplier.rfq.show')
            ->layout('layouts.app');
    }
}
