<?php

namespace App\Livewire\Supplier\Quotes;

use App\Events\QuoteSubmitted;
use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithCalculation;
use App\Livewire\Traits\WithLogging;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Edit extends Component
{
    use Alert, WithCalculation, WithLogging;

    public Quote $quote;

    /**
     * Quote items - keyed by request_item_id
     * @var array<int, array<string, mixed>>
     */
    public array $items = [];

    public ?string $notes = null;
    public ?string $terms_conditions = null;
    public ?string $valid_until = null;
    public string $currency = 'USD';

    public function mount(Quote $quote): void
    {
        // Security check - only supplier can edit their own quote
        if ($quote->supplier_id !== Auth::id()) {
            abort(403, 'You can only edit your own quotes.');
        }

        // Can only edit draft or submitted quotes
        if (!in_array($quote->status, ['draft', 'submitted'])) {
            $this->error(__('This quote cannot be edited anymore.'));
            $this->redirect(route('supplier.quotes.index'));
            return;
        }

        $this->quote = $quote->load(['request.items', 'items']);

        $this->logPageView('Supplier Quote Edit Form');

        // Load existing quote data
        $this->currency = $this->quote->currency ?? 'USD';
        $this->valid_until = $this->quote->valid_until?->format('Y-m-d');
        $this->notes = $this->quote->notes;
        $this->terms_conditions = $this->quote->terms_conditions;

        // Initialize items from existing quote items
        foreach ($this->quote->items as $item) {
            $this->items[$item->request_item_id] = [
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate ?? 0,
                'notes' => $item->notes,
            ];
        }
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:500'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
            'valid_until' => ['required', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms_conditions' => ['nullable', 'string', 'max:2000'],
            'currency' => ['required', 'string', 'size:3'],
        ];
    }

    public function updated($propertyName): void
    {
        if (preg_match('/items\.\d+\.(quantity|unit_price|tax_rate)/', $propertyName)) {
            $this->triggerCalculationToast($propertyName);
        }
    }

    public function calculateTotal(): float
    {
        $total = 0;

        foreach ($this->items as $item) {
            if (isset($item['quantity']) && isset($item['unit_price'])) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $tax = $subtotal * (($item['tax_rate'] ?? 0) / 100);
                $total += $subtotal + $tax;
            }
        }

        return $total;
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        if (!$user) {
            $this->error(__('You must be logged in to update a quote.'));
            return;
        }

        $totalAmount = $this->calculateTotal();

        if ($totalAmount <= 0) {
            $this->error(__('Please provide valid pricing for all items.'));
            return;
        }

        // Update the quote
        $this->quote->update([
            'total_amount' => $totalAmount,
            'currency' => $this->currency,
            'valid_until' => $this->valid_until,
            'notes' => $this->notes,
            'terms_conditions' => $this->terms_conditions,
            'status' => 'submitted',
            'submitted_at' => $this->quote->submitted_at ?? now(),
        ]);

        // Delete existing quote items
        $this->quote->items()->delete();

        // Save updated quote items
        foreach ($this->items as $requestItemId => $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $tax = $subtotal * (($item['tax_rate'] ?? 0) / 100);
            $total = $subtotal + $tax;

            QuoteItem::create([
                'quote_id' => $this->quote->id,
                'request_item_id' => $requestItemId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'total' => $total,
                'notes' => $item['notes'],
            ]);
        }

        $this->logUpdate(
            model: Quote::class,
            modelId: $this->quote->id,
            changes: [
                'total_amount' => $totalAmount,
                'currency' => $this->currency,
                'valid_until' => $this->valid_until,
            ]
        );

        $this->success(__('Quote updated successfully!'));
        $this->redirect(route('supplier.quotes.index'));
    }

    public function render(): View
    {
        return view('livewire.supplier.quotes.edit')
            ->layout('layouts.app');
    }
}
