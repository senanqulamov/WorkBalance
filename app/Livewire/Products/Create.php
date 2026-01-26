<?php

namespace App\Livewire\Products;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Market;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    use Alert, WithLogging;

    public Product $product;

    public bool $modal = false;

    public function mount(): void
    {
        $this->product = new Product;
    }

    public function render(): View
    {
        $user = Auth::user();
        $markets = $user && $user->isSeller()
            ? Market::where('user_id', $user->id)->orderBy('name')->get()
            : Market::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('livewire.products.create', compact('markets', 'categories'));
    }

    public function rules(): array
    {
        return [
            'product.name' => [
                'required',
                'string',
                'max:255',
            ],
            'product.sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku'),
            ],
            'product.price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'product.stock' => [
                'required',
                'integer',
                'min:0',
            ],
            'product.category_id' => [
                'required',
                'exists:categories,id',
            ],
            'product.market_id' => [
                'required',
                'exists:markets,id',
            ],
        ];
    }

    public function save(): void
    {
        // Check permission
        if (! Auth::user()->hasPermission('create_products')) {
            $this->error('You do not have permission to create products.');

            return;
        }

        $user = Auth::user();

        // Additional safety: if seller, ensure selected market belongs to them
        if ($user && $user->isSeller()) {
            $ownsMarket = Market::where('id', $this->product->market_id)
                ->where('user_id', $user->id)
                ->exists();

            if (! $ownsMarket) {
                $this->error('You can only list products in your own markets.');

                return;
            }

            // Ensure supplier_id is set to current seller
            $this->product->supplier_id = $user->id;
        }

        $this->validate();
        $this->product->save();
        $this->logCreate(Product::class, $this->product->id, [
            'name' => $this->product->name,
            'sku' => $this->product->sku,
            'price' => $this->product->price,
            'market_id' => $this->product->market_id,
        ]);

        $this->dispatch('created');

        $this->reset();
        $this->product = new Product;

        $this->success();
    }
}
