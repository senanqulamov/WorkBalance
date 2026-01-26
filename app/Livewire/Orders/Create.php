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
use Livewire\Component;

class Create extends Component
{
    use Alert, WithCalculation, WithLogging;

    public Order $order;

    public bool $modal = false;

    /**
     * Flat list of order items.
     * Each item: [market_id, product_id, user_id, quantity, unit_price]
     */
    public array $items = [];

    /** @var array<int, array<int, array<string, mixed>>> */
    public array $productsByMarket = [];

    /** Preloaded markets with product counts and label/value for selects. */
    public $marketOptions;

    public function mount(): void
    {
        $this->order = new Order;
        $this->order->order_number = Order::generateOrderNumber();
        $this->order->status = 'processing';

        $this->items = [
            $this->makeEmptyItem(),
        ];

        $this->loadMarketOptions();
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

    protected function makeEmptyItem(): array
    {
        return [
            'market_id' => null,
            'product_id' => null,
            'user_id' => null,
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function render(): View
    {
        return view('livewire.orders.create', [
            'users' => User::orderBy('name')->get(),
            'markets' => $this->marketOptions,
        ]);
    }

    public function rules(): array
    {
        return [
            'order.order_number' => [
                'nullable',
                'string',
                'max:255',
                'unique:orders,order_number',
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
        if (!Auth::user()->hasPermission('create_orders')) {
            $this->error('You do not have permission to create orders.');
            return;
        }

        $this->normalizeItems();

        if (count($this->items) === 0) {
            $this->addError('items', __('Please add at least one product to the order.'));
            return;
        }

        $this->validate();

        $this->order->total = $this->calculateTotal();
        $this->order->save();

        foreach ($this->items as $item) {
            $this->order->items()->create([
                'product_id' => $item['product_id'],
                'market_id' => $item['market_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $this->logCreate(Order::class, $this->order->id, [
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
        ]);

        $this->success();

        $this->dispatch('created');

        $this->reset('items');
        $this->order = new Order;
        $this->order->status = 'processing';
        $this->items = [$this->makeEmptyItem()];

        $this->modal = false;
    }
}
