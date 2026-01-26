<?php

namespace App\Livewire\Products;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use App\Models\Product;
use Livewire\Attributes\Renderless;
use Livewire\Component;

class Delete extends Component
{
    use Alert, WithLogging;

    public Product $product;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <x-button.circle icon="trash" color="red" wire:click="confirm" />
        </div>
        HTML;
    }

    #[Renderless]
    public function confirm(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('delete_products')) {
            $this->error('You do not have permission to delete products.');
            return;
        }

        $this->question()
            ->confirm(method: 'delete')
            ->cancel()
            ->send();
    }

    public function delete(): void
    {
        // Check permission
        if (!Auth::user()->hasPermission('delete_products')) {
            $this->error('You do not have permission to delete products.');
            return;
        }

        $productData = ['name' => $this->product->name, 'sku' => $this->product->sku];
        $productId = $this->product->id;

        $this->product->delete();
        $this->logDelete(Product::class, $productId, $productData);


        $this->dispatch('deleted');

        $this->success();
    }
}
