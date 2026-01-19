<?php

namespace App\Livewire\Supplier\Orders;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithCalculation;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Edit extends Component
{
    use Alert, WithCalculation, WithLogging;

    public Order $order;

    /**
     * Flat list of order items.
     * Each item: [market_id, product_id, quantity, unit_price]
     */
    public array $items = [];

    public ?string $notes = null;

    /** @var array<int, array<int, array<string, mixed>>> */
    public array $productsByMarket = [];

    /** Preloaded markets with product counts and label/value for selects. */
    public $marketOptions;

    public function mount(Order $order): void
    {
        // Ensure supplier can only edit their own pending orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        if ($order->status !== Order::STATUS_PENDING) {
            $this->error(__('Only pending orders can be edited'));
            $this->redirect(route('supplier.orders.show', $order));
            return;
        }

        $this->order = $order->load(['items.product', 'items.market']);
        $this->notes = $order->notes;

        // Load existing items
        foreach ($this->order->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'market_id' => $item->market_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'max_stock' => $item->product->stock + $item->quantity, // Current stock + what was taken
            ];

            // Cache products for this market
            $this->cacheProductsForMarket($item->market_id);
        }

        $this->loadMarketOptions();
    }

    protected function loadMarketOptions(): void
    {
        $this->marketOptions = Market::with('seller')
            ->withCount('products')
            ->whereHas('products', function ($query) {
                $query->where('stock', '>', 0);
            })
            ->orderBy('name')
            ->get()
            ->map(fn ($market) => [
                'id' => $market->id,
                'name' => sprintf('%s - %s (%d products)', $market->name, $market->seller?->name ?? 'Unknown', $market->products_count),
                'seller_name' => $market->seller?->name ?? 'Unknown',
                'products_count' => $market->products_count,
            ]);
    }

    protected function makeEmptyItem(): array
    {
        return [
            'id' => null,
            'market_id' => null,
            'product_id' => null,
            'quantity' => 1,
            'unit_price' => 0,
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
                if ($product && isset($product['price'], $product['stock'])) {
                    $this->items[$index]['unit_price'] = (float) $product['price'];
                    $this->items[$index]['max_stock'] = (int) $product['stock'];

                    return;
                }
            }

            $product = Product::find($value);
            if ($product) {
                $this->items[$index]['unit_price'] = (float) $product->price;
                $this->items[$index]['max_stock'] = (int) $product->stock;
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
                ->select(['id', 'name', 'price', 'stock', 'sku'])
                ->where('market_id', $marketId)
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get()
                ->map(fn (Product $product) => [
                    'id' => $product->id,
                    'name' => sprintf('%s (Stock: %d) - $%s', $product->name, $product->stock, number_format($product->price, 2)),
                    'price' => (float) $product->price,
                    'stock' => (int) $product->stock,
                    'sku' => $product->sku,
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

    public function rules(): array
    {
        return [
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
            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    #[Renderless]
    public function confirmUpdate(): void
    {
        $this->normalizeItems();

        if (count($this->items) === 0) {
            $this->error(__('Please add at least one product to the order'));
            return;
        }

        $this->question(
            __('Are you sure you want to update this order?'),
            __('Update Order?')
        )
            ->confirm(method: 'update')
            ->cancel()
            ->send();
    }

    public function update(): void
    {
        $this->normalizeItems();

        if (count($this->items) === 0) {
            $this->error(__('Please add at least one product to the order'));
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // First, restore stock for all existing items
            foreach ($this->order->items as $oldItem) {
                $oldItem->product->increment('stock', $oldItem->quantity);
            }

            // Delete all existing items
            $this->order->items()->delete();

            // Calculate new total
            $orderTotal = 0;
            foreach ($this->items as $item) {
                $orderTotal += $item['quantity'] * $item['unit_price'];
            }

            // Update order
            $this->order->update([
                'total' => $orderTotal,
                'notes' => $this->notes,
            ]);

            // Add new items and deduct stock
            foreach ($this->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (!$product) {
                    throw new \Exception(__('Product not found'));
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception(__('Insufficient stock for :name', ['name' => $product->name]));
                }

                $this->order->items()->create([
                    'product_id' => $item['product_id'],
                    'market_id' => $item['market_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            $this->logUpdate(Order::class, $this->order->id, [
                'order_number' => $this->order->order_number,
                'total' => $this->order->total,
                'items_count' => count($this->items),
            ]);

            DB::commit();

            $this->success(__('Order updated successfully!'));

            $this->redirect(route('supplier.orders.show', $this->order));
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->error(__('Failed to update order: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render(): View
    {
        return view('livewire.supplier.orders.edit', [
            'markets' => $this->marketOptions,
        ])
            ->layout('layouts.app');
    }
}
