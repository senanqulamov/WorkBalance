<?php

namespace App\Livewire\Rfq;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Product;
use App\Models\Request;
use App\Models\RequestItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithLogging;

    public ?Request $request = null;

    public bool $modal = false;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $items = [];

    public function render(): View
    {
        return view('livewire.rfq.update', [
            'productNames' => Product::query()
                ->select('name')
                ->distinct()
                ->orderBy('name')
                ->limit(100)
                ->pluck('name')
                ->toArray(),
        ]);
    }

    #[On('load::rfq')]
    public function load(int $rfq): void
    {
        $request = Request::with(['items', 'buyer'])->find($rfq);

        if (! $request) {
            $this->error(__('The requested RFQ could not be found.'));
            $this->modal = false;

            return;
        }

        $this->request = $request;

        $this->items = [];
        foreach ($this->request->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'specifications' => $item->specifications,
            ];
        }

        if (empty($this->items)) {
            $this->items[] = [
                'id' => null,
                'product_name' => '',
                'quantity' => 1,
                'specifications' => null,
            ];
        }

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'request.title' => ['required', 'string', 'max:255'],
            'request.description' => ['nullable', 'string'],
            'request.deadline' => ['required', 'date', 'after:today'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.specifications' => ['nullable', 'string'],
        ];
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => null,
            'product_name' => '',
            'quantity' => 1,
            'specifications' => null,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    /**
     * Remove any incomplete/empty items before validation.
     */
    protected function normalizeItems(): void
    {
        $normalized = [];

        foreach ($this->items as $item) {
            if (! isset($item['product_name']) || trim($item['product_name']) === '') {
                continue;
            }

            if (! isset($item['quantity']) || $item['quantity'] <= 0) {
                continue;
            }

            $normalized[] = $item;
        }

        $this->items = $normalized;
    }

    protected function syncItems(): void
    {
        if (! $this->request) {
            return;
        }

        $existingIds = $this->request->items()->pluck('id')->all();
        $keptIds = [];

        foreach ($this->items as $itemData) {
            $id = $itemData['id'] ?? null;

            if ($id) {
                $item = $this->request->items()->find($id);
                if (! $item) {
                    continue;
                }
            } else {
                $item = new RequestItem(['request_id' => $this->request->id]);
            }

            $item->product_name = trim($itemData['product_name']);
            $item->quantity = $itemData['quantity'];
            $item->specifications = $itemData['specifications'] ?? null;
            $item->save();

            $keptIds[] = $item->id;
        }

        // Delete removed items
        $toDelete = array_diff($existingIds, $keptIds);
        if (! empty($toDelete)) {
            RequestItem::whereIn('id', $toDelete)->delete();
        }
    }

    public function save(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('edit_rfqs')) {
            $this->error('You do not have permission to edit RFQs.');
            return;
        }

        if (! $this->request) {
            return;
        }

        $this->normalizeItems();

        if (count($this->items) === 0) {
            $this->addError('items', __('Please add at least one item to the request.'));

            return;
        }

        $this->validate();

        $changes = $this->request->getDirty();
        $this->request->save();

        $this->syncItems();

        $this->logUpdate(Request::class, $this->request->id, $changes);

        $this->dispatch('updated');

        $this->resetExcept('request');

        $this->success();
    }
}
