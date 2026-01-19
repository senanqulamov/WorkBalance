<?php

namespace App\Livewire\Buyer\Rfq;

use App\Events\SupplierInvited;
use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Category;
use App\Models\Product;
use App\Models\Request;
use App\Models\RequestItem;
use App\Models\SupplierInvitation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    use Alert, WithLogging;

    public Request $request;

    public bool $modal = false;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $items = [];

    /**
     * @var array<int, int>
     */
    public array $selectedSuppliers = [];

    /** @var array<int, array<int, array<string, mixed>>> */
    public array $productsByCategory = [];

    /** Preloaded categories with product counts */
    public $categoryOptions;

    public function mount(): void
    {
        $this->request = new Request;
        $this->request->status = 'draft';

        $this->items = [
            $this->makeEmptyItem(),
        ];

        $this->loadCategoryOptions();
    }

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

    protected function makeEmptyItem(): array
    {
        return [
            'category_id' => null,
            'product_id' => null,
            'product_name' => '',
            'quantity' => 1,
            'specifications' => null,
        ];
    }

    #[On('buyer::rfq::create::open')]
    public function openModal(): void
    {
        $this->modal = true;
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
        // $key format: "0.category_id" or "1.product_id" etc.
        if (str_ends_with($key, '.category_id')) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];
            $categoryId = (int) $value;

            if ($categoryId) {
                $this->cacheProductsForCategory($categoryId);
            }

            $this->items[$index]['product_id'] = null;
            $this->items[$index]['product_name'] = '';

            return;
        }

        if (str_ends_with($key, '.product_id')) {
            $parts = explode('.', $key);
            $index = (int) $parts[0];
            $productName = $value;

            if ($productName) {
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
                    'id' => $product->name,
                    'name' => $product->name,
                ])
                ->all();
        }

        return $this->productsByCategory[$categoryId];
    }

    protected function resolveProductFromCache(int $productId, ?int $categoryId = null): ?array
    {
        return null;
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

        // Observer will fire RequestStatusChanged event automatically

        foreach ($this->items as $item) {
            RequestItem::create([
                'request_id' => $this->request->id,
                'product_name' => trim($item['product_name']),
                'quantity' => $item['quantity'],
                'specifications' => $item['specifications'] ?? null,
            ]);
        }

        // Create supplier invitations
        if (!empty($this->selectedSuppliers)) {
            foreach ($this->selectedSuppliers as $supplierId) {
                $invitation = SupplierInvitation::create([
                    'request_id' => $this->request->id,
                    'supplier_id' => $supplierId,
                    'status' => 'pending',
                    'sent_at' => now(),
                ]);

                // Dispatch event to send notification
                $supplier = User::find($supplierId);
                if ($supplier) {
                    event(new SupplierInvited($invitation, $user));
                }
            }
        }

        $this->logCreate(
            Request::class,
            $this->request->id,
            [
                'title' => $this->request->title,
                'deadline' => $this->request->deadline,
                'items_count' => count($this->items),
                'suppliers_invited' => count($this->selectedSuppliers),
                'buyer_id' => $this->request->buyer_id,
            ]
        );

        $this->dispatch('created', id: $this->request->id);

        $this->reset();
        $this->request = new Request;
        $this->request->status = 'draft';
        $this->items = [$this->makeEmptyItem()];
        $this->selectedSuppliers = [];
        $this->productsByCategory = [];
        $this->categoryOptions = null;
        $this->loadCategoryOptions();

        $this->success(__('RFQ created successfully.'));
    }

    public function getCategoriesProperty()
    {
        if (empty($this->categoryOptions)) {
            $this->loadCategoryOptions();
        }
        return $this->categoryOptions;
    }

    public function render(): View
    {
        return view('livewire.buyer.rfq.create', [
            'categories' => $this->categories,
            'suppliers' => User::activeSuppliers()->orderBy('name')->get(),
        ]);
    }
}
