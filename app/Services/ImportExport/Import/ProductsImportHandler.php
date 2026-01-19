<?php

namespace App\Services\ImportExport\Import;

use App\Models\Product;
use App\Models\Market;
use App\Models\Category;
use App\Services\ImportExport\BaseImportHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductsImportHandler extends BaseImportHandler
{
    public function import(string $filePath): array
    {
        $fileType = $this->detectFileType($filePath);
        $data = $fileType === 'csv' ? $this->readCsvFile($filePath) : $this->readExcelFile($filePath);

        $success = 0;
        $errors = 0;
        $errorMessages = [];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                try {
                    $this->importRow($row);
                    $success++;
                } catch (\Exception $e) {
                    $errors++;
                    $errorMessages[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'success' => $success,
            'errors' => $errors,
            'error_messages' => $errorMessages,
        ];
    }

    protected function importRow(array $row): void
    {
        // Find or create market
        $market = Market::where('name', $row['market_name'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$market) {
            throw new \Exception("Market '{$row['market_name']}' not found");
        }

        // Check if SKU already exists
        $existing = Product::where('sku', $row['sku'])->first();
        if ($existing) {
            throw new \Exception("SKU '{$row['sku']}' already exists");
        }

        // Find or create category by name
        $categoryId = null;
        if (!empty($row['category'])) {
            $category = Category::firstOrCreate(['name' => $row['category']]);
            $categoryId = $category->id;
        }

        Product::create([
            'name' => $row['name'],
            'sku' => $row['sku'],
            'price' => (float) $row['price'],
            'stock' => (int) $row['stock'],
            'category_id' => $categoryId,
            'market_id' => $market->id,
            'supplier_id' => Auth::id(),
        ]);
    }

    public function generateTemplate(): string
    {
        $filename = 'products_import_template_' . date('Y-m-d_His') . '.csv';
        $path = 'templates/' . $filename;
        $fullPath = storage_path('app/' . $path);

        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $handle = fopen($fullPath, 'w');

        // Write headers
        fputcsv($handle, ['name', 'sku', 'price', 'stock', 'category', 'market_name']);

        // Write example data
        fputcsv($handle, ['Laptop HP 15', 'LAP-HP-001', '599.99', '50', 'Electronics', 'Tech Market']);
        fputcsv($handle, ['Mouse Wireless', 'MOU-WL-001', '19.99', '200', 'Accessories', 'Tech Market']);

        fclose($handle);

        return $path;
    }
}
