<?php

namespace App\Livewire\Supplier\Quotes;

use App\Events\QuoteSubmitted;
use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithCalculation;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\SupplierInvitation;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use Alert, WithCalculation, WithFileUploads;

    public SupplierInvitation $invitation;
    public array $items = [];
    public ?string $notes = null;
    public ?string $terms_conditions = null;
    public $valid_until = null;
    public array $attachments = [];

    public function mount(SupplierInvitation $invitation): void
    {
        if ($invitation->supplier_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this invitation.');
        }

        if ($invitation->status !== 'pending' && $invitation->status !== 'accepted') {
            abort(403, 'This invitation is no longer available for quoting.');
        }

        $this->invitation = $invitation;

        foreach ($invitation->request->items as $requestItem) {
            $this->items[] = [
                'request_item_id' => $requestItem->id,
                'description' => $requestItem->description,
                'quantity' => $requestItem->quantity,
                'unit_price' => null,
                'tax_rate' => 0,
                'notes' => null,
            ];
        }

        $this->valid_until = now()->addDays(30)->format('Y-m-d');
    }

    public function updated($propertyName): void
    {
        if (preg_match('/items\.\d+\.(quantity|unit_price|tax_rate)/', $propertyName)) {
            $this->triggerCalculationToast($propertyName);
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'request_item_id' => null,
            'description' => '',
            'quantity' => 1,
            'unit_price' => null,
            'tax_rate' => 0,
            'notes' => null,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function calculateTotal(): float
    {
        $total = 0;
        foreach ($this->items as $item) {
            if (isset($item['quantity']) && isset($item['unit_price'])) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $tax = $subtotal * ($item['tax_rate'] ?? 0) / 100;
                $total += $subtotal + $tax;
            }
        }
        return $total;
    }

    public function save(): void
    {
        $this->validate([
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0|max:100',
            'valid_until' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
            'terms_conditions' => 'nullable|string|max:2000',
        ]);

        // Create the quote
        $quote = Quote::create([
            'request_id' => $this->invitation->request_id,
            'supplier_id' => auth()->id(),
            'supplier_invitation_id' => $this->invitation->id,
            'total_amount' => $this->calculateTotal(),
            'currency' => auth()->user()->currency ?? 'USD',
            'valid_until' => $this->valid_until,
            'notes' => $this->notes,
            'terms_conditions' => $this->terms_conditions,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Create quote items
        foreach ($this->items as $item) {
            QuoteItem::create([
                'quote_id' => $quote->id,
                'request_item_id' => $item['request_item_id'],
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'notes' => $item['notes'],
            ]);
        }

        // Update invitation status
        $this->invitation->update([
            'status' => 'quoted',
        ]);

        // Fire event
        event(new QuoteSubmitted($quote, auth()->user()));

        session()->flash('success', 'Quote submitted successfully!');
        $this->redirect(route('supplier.quotes.index'));
    }

    public function render(): View
    {
        return view('livewire.supplier.quotes.create')
            ->layout('layouts.app');
    }
}
