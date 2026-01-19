<?php

namespace App\Livewire\Supplier\Rfq;

use App\Events\QuoteSubmitted;
use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithCalculation;
use App\Livewire\Traits\WithLogging;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Request;
use App\Models\SupplierInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QuoteForm extends Component
{
    use Alert, WithCalculation, WithLogging;

    public Request $request;

    /**
     * Quote items - keyed by request_item_id
     * @var array<int, array<string, mixed>>
     */
    public array $items = [];

    public ?string $notes = null;
    public ?string $terms_conditions = null;
    public ?string $valid_until = null;
    public string $currency = 'USD';

    public function mount(Request $request): void
    {
        $this->request = $request->load('items');

        $this->logPageView('Supplier RFQ Quote Form');

        $user = Auth::user();

        if (! $user) {
            abort(403, 'You must be logged in to submit a quote.');
        }

        if ($user->id === $this->request->buyer_id) {
            abort(403, 'Suppliers cannot quote on RFQs they created.');
        }

        if ($this->request->status !== 'open' || ($this->request->deadline && $this->request->deadline->isPast())) {
            $this->error(__('This RFQ is not open for quotes.'));
            abort(403, 'This RFQ is not open for quotes.');
        }

        // Check if supplier already submitted a quote
        $existingQuote = Quote::where('request_id', $this->request->id)
            ->where('supplier_id', $user->id)
            ->first();

        if ($existingQuote) {
            $this->error(__('You have already submitted a quote for this RFQ.'));
            $this->redirect(route('supplier.rfq.show', $this->request));
            return;
        }

        // Initialize items from request items
        foreach ($this->request->items as $item) {
            $this->items[$item->id] = [
                'description' => $item->product_name ?? 'Product Item',
                'quantity' => $item->quantity,
                'unit_price' => null,
                'tax_rate' => 0,
                'notes' => null,
            ];
        }

        // Set default valid_until to 30 days from now
        $this->valid_until = now()->addDays(30)->format('Y-m-d');

        // Set currency from user or default
        $this->currency = $user->currency ?? 'USD';
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

        if (! $user) {
            $this->error(__('You must be logged in to submit a quote.'));
            return;
        }

        $totalAmount = $this->calculateTotal();

        if ($totalAmount <= 0) {
            $this->error(__('Please provide valid pricing for all items.'));
            return;
        }

        // Find supplier invitation (if exists)
        $invitation = SupplierInvitation::where('request_id', $this->request->id)
            ->where('supplier_id', $user->id)
            ->first();

        // Create the quote
        $quote = Quote::create([
            'request_id' => $this->request->id,
            'supplier_id' => $user->id,
            'supplier_invitation_id' => $invitation?->id,
            'unit_price' => 0,
            'total_price' => 0,
            'total_amount' => $totalAmount,
            'currency' => $this->currency,
            'valid_until' => $this->valid_until,
            'notes' => $this->notes,
            'terms_conditions' => $this->terms_conditions,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Save quote items
        foreach ($this->items as $requestItemId => $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $tax = $subtotal * (($item['tax_rate'] ?? 0) / 100);
            $total = $subtotal + $tax;

            QuoteItem::create([
                'quote_id' => $quote->id,
                'request_item_id' => $requestItemId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'total' => $total,
                'notes' => $item['notes'],
            ]);
        }

        // Update invitation status if exists
        if ($invitation && $invitation->status === 'accepted') {
            $invitation->update(['status' => 'quoted']);
        }

        // Fire event
        event(new QuoteSubmitted($quote));

        $this->logCreate(
            model: Quote::class,
            modelId: $quote->id,
            data: [
                'request_id' => $this->request->id,
                'total_amount' => $totalAmount,
                'currency' => $this->currency,
            ]
        );

        $this->success(__('Quote submitted successfully!'));
        $this->redirect(route('supplier.quotes.index'));
    }

    public function render(): View
    {
        return view('livewire.supplier.rfq.quote-form')
            ->layout('layouts.app');
    }
}
