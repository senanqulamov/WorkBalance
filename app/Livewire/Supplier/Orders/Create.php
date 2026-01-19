<?php

namespace App\Livewire\Supplier\Orders;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithCalculation;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Create extends Component
{
    use Alert, WithCalculation, WithLogging;

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

    // Product search and comparison
    public ?string $searchQuery = null;
    public array $searchResults = [];
    public bool $showSearchResults = false;

    /**
     * Initialize component state.
     */
    public function mount(): void
    {
        $this->items = [
            $this->makeEmptyItem(),
        ];

        $this->loadMarketOptions();
    }

    /**
     * Triggered when searchQuery is updated
     */
    public function updatedSearchQuery(): void
    {
        $this->searchProducts();
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

    /**
     * Search for products across all markets
     */
    public function searchProducts(): void
    {
        \Log::info('Search triggered', ['query' => $this->searchQuery, 'length' => strlen($this->searchQuery ?? '')]);

        if (empty($this->searchQuery) || strlen($this->searchQuery) < 2) {
            $this->searchResults = [];
            $this->showSearchResults = false;
            return;
        }

        try {
            $query = trim($this->searchQuery);

            \Log::info('Searching for products', ['query' => $query]);

            // Search for similar products across all markets with stock
            $products = Product::query()
                ->select(['products.id', 'products.name', 'products.sku', 'products.price', 'products.stock', 'products.market_id', 'products.category_id', 'categories.name as category_name'])
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->with(['market.seller'])
                ->where('stock', '>', 0)
                ->where(function ($q) use ($query) {
                    $q->where('products.name', 'like', "%{$query}%")
                      ->orWhere('products.sku', 'like', "%{$query}%")
                      ->orWhere('categories.name', 'like', "%{$query}%");
                })
                ->orderBy('products.name')
                ->limit(50)
                ->get();

            \Log::info('Products found', ['count' => $products->count()]);

            // Group similar products by name for comparison
            $grouped = $products->groupBy(function ($product) {
                // Normalize product name for grouping
                return strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', '', $product->name)));
            });

            $this->searchResults = $grouped->map(function ($group) {
                return [
                    'name' => $group->first()->name,
                    'category' => $group->first()->category_name,
                    'products' => $group->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'sku' => $product->sku,
                            'price' => (float) $product->price,
                            'stock' => (int) $product->stock,
                            'market_id' => $product->market_id,
                            'market_name' => $product->market?->name ?? 'Unknown',
                            'seller_name' => $product->market?->seller?->name ?? 'Unknown',
                            'category' => $product->category_name,
                        ];
                    })->sortBy('price')->values()->all(),
                    'min_price' => (float) $group->min('price'),
                    'max_price' => (float) $group->max('price'),
                    'total_stock' => (int) $group->sum('stock'),
                    'markets_count' => $group->pluck('market_id')->unique()->count(),
                ];
            })->values()->all();

            \Log::info('Search results', ['groups' => count($this->searchResults)]);

            $this->showSearchResults = true;
        } catch (\Exception $e) {
            \Log::error('Search failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->error(__('Search failed. Please try again.'));
            $this->searchResults = [];
            $this->showSearchResults = false;
        }
    }

    /**
     * Add product from search results to order
     */
    public function addProductFromSearch(int $productId): void
    {
        $product = Product::with('market')->find($productId);

        if (!$product || $product->stock < 1) {
            $this->error(__('Product is out of stock'));
            return;
        }

        // Find the first empty item or add a new one
        $emptyIndex = null;
        foreach ($this->items as $index => $item) {
            if (empty($item['product_id'])) {
                $emptyIndex = $index;
                break;
            }
        }

        if ($emptyIndex === null) {
            $this->items[] = $this->makeEmptyItem();
            $emptyIndex = count($this->items) - 1;
        }

        // Set the product details
        $this->items[$emptyIndex] = [
            'market_id' => $product->market_id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => (float) $product->price,
            'max_stock' => (int) $product->stock,
        ];

        // Cache products for this market
        $this->cacheProductsForMarket($product->market_id);

        $this->success(__('Product added to order'));
    }

    /**
     * Clear search results
     */
    public function clearSearch(): void
    {
        $this->searchQuery = null;
        $this->searchResults = [];
        $this->showSearchResults = false;
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
    public function confirmSave(): void
    {
        $this->normalizeItems();

        if (count($this->items) === 0) {
            $this->error(__('Please add at least one product to the order'));
            return;
        }

        $this->question(
            __('Are you sure you want to place this order?'),
            __('Place Order?')
        )
            ->confirm(method: 'save')
            ->cancel()
            ->send();
    }

    public function save(): void
    {
        $this->normalizeItems();

        if (count($this->items) === 0) {
            $this->error(__('Please add at least one product to the order'));
            return;
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Calculate total for the entire order
            $orderTotal = 0;
            foreach ($this->items as $item) {
                $orderTotal += $item['quantity'] * $item['unit_price'];
            }

            // Create ONE order with all items
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $orderTotal,
                'status' => Order::STATUS_PENDING,
                'notes' => $this->notes,
            ]);

            // Add all items to this single order
            foreach ($this->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (!$product) {
                    throw new \Exception(__('Product not found'));
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception(__('Insufficient stock for :name', ['name' => $product->name]));
                }

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'market_id' => $item['market_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            $this->logCreate(Order::class, $order->id, [
                'order_number' => $order->order_number,
                'total' => $order->total,
                'items_count' => count($this->items),
            ]);

            DB::commit();

            $this->success(__('Order placed successfully! Order number: :number', ['number' => $order->order_number]));

            $this->reset(['items', 'notes']);
            $this->items = [$this->makeEmptyItem()];
            $this->loadMarketOptions();

            $this->redirect(route('supplier.orders.index'));
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->error(__('Failed to place order: :error', ['error' => $e->getMessage()]));
        }
    }

    public function render(): View
    {
        return view('livewire.supplier.orders.create', [
            'markets' => $this->marketOptions,
        ])
            ->layout('layouts.app');
    }
}
