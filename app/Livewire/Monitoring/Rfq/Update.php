<?php

namespace App\Livewire\Monitoring\Rfq;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Category;
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

    /** @var array<int, array<int, array<string, mixed>>> */
    public array $productsByCategory = [];

    /** Preloaded categories with product counts */
    public $categoryOptions;

    protected function loadCategoryOptions(): void
    {
        $this->categoryOptions = Category::query()
            ->withCount('products')
            ->whereHas('products')
            ->orderBy('name')
            ->get()
            ->map(fn ($category) => [
                'id' => $category->id,
                'name' => sprintf('%s (%d products)', $category->name, $category->products_count),
                'products_count' => $category->products_count,
            ]);
    }

    public function getCategoriesProperty()
    {
        if (empty($this->categoryOptions)) {
            $this->loadCategoryOptions();
        }
        return $this->categoryOptions;
    }

    public function updatedItems($value, $key): void
    {
        // $key format: "0.category_id" or "1.product_id" etc.
        if (str_ends_with($key, '.category_id')) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];
            $categoryId = (int) $value;

            if ($categoryId) {
                $this->cacheProductsForCategory($categoryId);
            }

            // Reset product selection when category changes
            $this->items[$index]['product_id'] = null;
            $this->items[$index]['product_name'] = '';

            return;
        }

        if (str_ends_with($key, '.product_id')) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];
            $productName = $value; // Now it's the product name (string), not ID

            if ($productName) {
                // Set the product name directly
                $this->items[$index]['product_name'] = $productName;
            }

            return;
        }
    }

    public function getProductsForCategory($categoryId): array
    {
        $categoryId = (int) $categoryId;

        if (! $categoryId) {
            return [];
        }

        return $this->cacheProductsForCategory($categoryId);
    }

    protected function cacheProductsForCategory(int $categoryId): array
    {
        if ($categoryId <= 0) {
            return [];
        }

        if (! isset($this->productsByCategory[$categoryId])) {
            // Get unique product names from the category
            $this->productsByCategory[$categoryId] = Product::query()
                ->select('name')
                ->where('category_id', $categoryId)
                ->distinct()
                ->orderBy('name')
                ->get()
                ->map(fn (Product $product) => [
                    'id' => $product->name, // Use name as ID for grouped products
                    'name' => $product->name,
                ])
                ->all();
        }

        return $this->productsByCategory[$categoryId];
    }

    public function render(): View
    {
        return view('livewire.monitoring.rfq.update', [
            'categories' => $this->categories,
        ]);
    }

    #[On('monitoring::load::rfq')]
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
            $product = Product::where('name', $item->product_name)->first();

            $this->items[] = [
                'id' => $item->id,
                'category_id' => $product?->category_id ?? null,
                'product_id' => null,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'specifications' => $item->specifications,
            ];

            // Cache products for the category if found
            if ($product && $product->category_id) {
                $this->cacheProductsForCategory($product->category_id);
            }
        }

        if (empty($this->items)) {
            $this->items[] = [
                'id' => null,
                'category_id' => null,
                'product_id' => null,
                'product_name' => '',
                'quantity' => 1,
                'specifications' => null,
            ];
        }

        $this->loadCategoryOptions();
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
            'category_id' => null,
            'product_id' => null,
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
        if (! $this->request) {
            return;
        }

        // Ensure the buyer owns this RFQ
        if (!Auth::user()->hasRole('admin')) {
            $this->error(__('You do not have permission to edit this RFQ.'));
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

        $this->modal = false;

        $this->success(__('RFQ updated successfully.'));
    }
}
