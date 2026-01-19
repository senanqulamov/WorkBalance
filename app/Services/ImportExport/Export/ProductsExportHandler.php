<?php

namespace App\Services\ImportExport\Export;

use App\Models\Product;
use App\Services\ImportExport\BaseExportHandler;
use Illuminate\Support\Facades\Auth;

class ProductsExportHandler extends BaseExportHandler
{
    public function export(array $filters = []): string
    {
        $data = $this->getData($filters);
        $filename = 'products_export_' . date('Y-m-d_His') . '.xlsx';

        return $this->generateExcelFile($data, $filename);
    }

    protected function getData(array $filters): array
    {
        $query = Product::with(['market'])
            ->where('supplier_id', Auth::id());

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                $product->id,
                $product->name,
                $product->sku,
                $product->price,
                $product->stock,
                $product->category?->name ?? '',
                $product->market?->name ?? '',
                $product->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    protected function getHeaders(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Price',
            'Stock',
            'Category',
            'Market',
            'Created At',
        ];
    }
}
