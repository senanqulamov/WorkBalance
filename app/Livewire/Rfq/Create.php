<?php

namespace App\Livewire\Rfq;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Product;
use App\Models\Request;
use App\Models\RequestItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    use Alert, WithLogging;

    public Request $request;

    /** Whether this form is in a modal (parity with Products\Create). */
    public bool $modal = false;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $items = [];

    public function mount(): void
    {
        $this->logPageView('RFQ Create');

        $this->request = new Request;
        $this->request->status = 'draft';

        $this->items = [
            $this->makeEmptyItem(),
        ];
    }

    protected function makeEmptyItem(): array
    {
        return [
            'product_id' => null,
            'quantity' => 1,
            'specifications' => null,
        ];
    }

    public function addItem(): void
    {
        $this->items[] = $this->makeEmptyItem();
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if (empty($this->items)) {
            $this->items[] = $this->makeEmptyItem();
        }
    }

    public function rules(): array
    {
        return [
            'request.title' => ['required', 'string', 'max:255'],
            'request.description' => ['nullable', 'string'],
            'request.deadline' => ['required', 'date', 'after:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.specifications' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $user = Auth::user();

        if (! $user) {
            $this->error(__('You must be logged in to create an RFQ.'));

            return;
        }

        $this->request->buyer_id = $user->id;
        $this->request->status = $this->request->status ?: 'draft';
        $this->request->save();

        foreach ($this->items as $item) {
            RequestItem::create([
                'request_id' => $this->request->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'specifications' => $item['specifications'] ?? null,
            ]);
        }

        $this->logCreate(
            Request::class,
            $this->request->id,
            [
                'title' => $this->request->title,
                'deadline' => $this->request->deadline,
                'items_count' => count($this->items),
                'buyer_id' => $this->request->buyer_id,
            ]
        );

        $this->dispatch('created', id: $this->request->id);

        $this->reset();
        $this->request = new Request;
        $this->request->status = 'draft';
        $this->items = [$this->makeEmptyItem()];

        $this->success(__('RFQ created successfully.'));
    }

    public function render(): View
    {
        return view('livewire.rfq.create', [
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
