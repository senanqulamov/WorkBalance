<?php

namespace App\Livewire\Orders;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithCalculation;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Update extends Component
{
    use Alert, WithCalculation, WithLogging;

    public ?Order $order = null;

    public bool $modal = false;

    /** Flat list of order items, used for editing existing orders. */
    public array $items = [];

    /** @var array<int, array<int, array<string, mixed>>> */
    public array $productsByMarket = [];

    /** Preloaded markets with product counts and label/value for selects. */
    public $marketOptions;

    public function render(): View
    {
        if ($this->marketOptions === null) {
            $this->loadMarketOptions();
        }

        return view('livewire.orders.update', [
            'users' => User::orderBy('name')->get(),
            'markets' => $this->marketOptions,
        ]);
    }

    protected function loadMarketOptions(): void
    {
        $this->marketOptions = Market::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn ($market) => [
                'id' => $market->id,
                'name' => sprintf('%s (%d)', $market->name, $market->products_count),
                'products_count' => $market->products_count,
            ]);
    }

    #[On('load::order')]
    public function load(Order $order): void
    {
        // Check ownership: only admins or the order owner can load for editing
        if (!Auth::user()->isAdmin() && $order->user_id !== Auth::id()) {
            $this->error('You can only edit your own orders.');
            return;
        }

        $this->order = $order->load(['items.product', 'items.market', 'user']);

        $this->items = [];
        foreach ($this->order->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'market_id' => $item->market_id,
                'product_id' => $item->product_id,
                'user_id' => $this->order->user_id,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ];

            if ($item->market_id) {
                $this->cacheProductsForMarket((int) $item->market_id);
            }
        }

        if (empty($this->items)) {
            $this->items[] = [
                'id' => null,
                'market_id' => null,
                'product_id' => null,
                'user_id' => $this->order->user_id,
                'quantity' => 1,
                'unit_price' => 0,
            ];
        }

        if ($this->marketOptions === null) {
            $this->loadMarketOptions();
        }

        $this->modal = true;
    }

    public function rules(): array
    {
        return [
            'order.order_number' => [
                'required',
                'string',
                'max:255',
                'unique:orders,order_number,'.$this->order?->id,
            ],
            'order.user_id' => [
                'required',
                'exists:users,id',
            ],
            'order.status' => [
                'required',
                'in:processing,completed,cancelled',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.market_id' => [
                'required',
                'exists:markets,id',
            ],
            'items.*.product_id' => [
                'required',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:1',
            ],
            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id' => null,
            'market_id' => null,
            'product_id' => null,
            'user_id' => $this->order?->user_id,
            'quantity' => 1,
            'unit_price' => 0,
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

    public function updatedItems($value, $key): void
    {
        // Trigger calculation toast for relevant fields
        $this->triggerCalculationToast("items.{$key}");

        // $key format: "0.market_id" or "1.product_id" etc.
        if (str_ends_with($key, '.market_id')) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];
            $marketId = (int) $value;

            if ($marketId) {
                $this->cacheProductsForMarket($marketId);
            }

            // Reset product and price when market changes
            $this->items[$index]['product_id'] = null;
            $this->items[$index]['unit_price'] = 0;

            return;
        }

        if (str_ends_with($key, '.product_id')) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];
            $productId = (int) $value;
            $marketId = (int) ($this->items[$index]['market_id'] ?? 0);

            if ($productId) {
                $product = $this->resolveProductFromCache($productId, $marketId);
                if ($product && isset($product['price'])) {
                    $this->items[$index]['unit_price'] = (float) $product['price'];

                    return;
                }
            }

            $product = Product::find($value);
            if ($product) {
                $this->items[$index]['unit_price'] = (float) $product->price;
            }

            return;
        }
    }

    public function getProductsForMarket($marketId): array
    {
        $marketId = (int) $marketId;

        if (! $marketId) {
            return [];
        }

        return $this->cacheProductsForMarket($marketId);
    }

    protected function cacheProductsForMarket(int $marketId): array
    {
        if ($marketId <= 0) {
            return [];
        }

        if (! isset($this->productsByMarket[$marketId])) {
            $this->productsByMarket[$marketId] = Product::query()
                ->select(['id', 'name', 'price'])
                ->where('market_id', $marketId)
                ->orderBy('name')
                ->get()
                ->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                ])
                ->all();
        }

        return $this->productsByMarket[$marketId];
    }

    protected function resolveProductFromCache(int $productId, ?int $marketId = null): ?array
    {
        if ($productId <= 0) {
            return null;
        }

        $groups = $marketId && isset($this->productsByMarket[$marketId])
            ? [$this->productsByMarket[$marketId]]
            : $this->productsByMarket;

        foreach ($groups as $products) {
            foreach ($products as $product) {
                if ((int) ($product['id'] ?? 0) === $productId) {
                    return $product;
                }
            }
        }

        return null;
    }

    public function calculateTotal(): float
    {
        $total = 0;

        foreach ($this->items as $item) {
            if (isset($item['quantity']) && isset($item['unit_price'])) {
                $total += $item['quantity'] * $item['unit_price'];
            }
        }

        return $total;
    }

    protected function normalizeItems(): void
    {
        $normalized = [];

        foreach ($this->items as $item) {
            if (! isset($item['market_id']) || ! $item['market_id']) {
                continue;
            }

            if (! isset($item['product_id']) || ! $item['product_id']) {
                continue;
            }

            if (! isset($item['quantity']) || $item['quantity'] <= 0) {
                continue;
            }

            if (! isset($item['unit_price']) || $item['unit_price'] < 0) {
                continue;
            }

            $normalized[] = $item;
        }

        $this->items = $normalized;
    }

    public function save(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('edit_orders')) {
            $this->error('You do not have permission to edit orders.');
            return;
        }

        // Check ownership: only admins or the order owner can edit
        if (!Auth::user()->isAdmin() && $this->order->user_id !== Auth::id()) {
            $this->error('You can only edit your own orders.');
            return;
        }

        $this->normalizeItems();

        if (count($this->items) === 0) {
            $this->addError('items', __('Please add at least one product to the order.'));
            return;
        }

        $this->validate();

        $newTotal = $this->calculateTotal();
        $this->order->total = $newTotal;
        $this->order->save();

        // Sync order items: update existing, create new, delete removed
        $existingItemIds = $this->order->items()->pluck('id')->all();
        $handledIds = [];

        foreach ($this->items as $itemData) {
            $payload = [
                'product_id' => $itemData['product_id'],
                'market_id' => $itemData['market_id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'subtotal' => $itemData['quantity'] * $itemData['unit_price'],
            ];

            if (! empty($itemData['id']) && in_array($itemData['id'], $existingItemIds, true)) {
                $orderItem = $this->order->items()->where('id', $itemData['id'])->first();
                if ($orderItem) {
                    $orderItem->update($payload);
                    $handledIds[] = $orderItem->id;
                }
            } else {
                $orderItem = $this->order->items()->create($payload);
                $handledIds[] = $orderItem->id;
            }
        }

        // Delete items that were removed from the UI
        $idsToDelete = array_diff($existingItemIds, $handledIds);
        if (! empty($idsToDelete)) {
            $this->order->items()->whereIn('id', $idsToDelete)->delete();
        }

        $this->logUpdate(Order::class, $this->order->id, [
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
        ]);

        $this->success();

        $this->dispatch('updated');

        $this->modal = false;
    }
}
