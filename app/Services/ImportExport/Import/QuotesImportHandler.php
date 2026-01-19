<?php

namespace App\Services\ImportExport\Import;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Request;
use App\Models\RequestItem;
use App\Services\ImportExport\BaseImportHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotesImportHandler extends BaseImportHandler
{
    public function import(string $filePath): array
    {
        $data = $this->readCsvFile($filePath);

        $success = 0;
        $errors = 0;
        $errorMessages = [];

        DB::beginTransaction();

        try {
            $currentQuote = null;
            $currentRfqId = null;

            foreach ($data as $index => $row) {
                try {
                    // If rfq_id is provided, start new quote
                    if (!empty($row['rfq_id'])) {
                        $currentRfqId = (int) $row['rfq_id'];
                        $currentQuote = $this->createQuote($row, $currentRfqId);
                        $success++;
                    }

                    // Add item to current quote
                    if ($currentQuote && !empty($row['product_name'])) {
                        $this->createQuoteItem($currentQuote, $row, $currentRfqId);
                    }
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

    protected function createQuote(array $row, int $rfqId): Quote
    {
        // Verify RFQ exists
        $request = Request::find($rfqId);
        if (!$request) {
            throw new \Exception("RFQ with ID {$rfqId} not found");
        }

        return Quote::create([
            'request_id' => $rfqId,
            'supplier_id' => Auth::id(),
            'total' => 0, // Will be calculated when items are added
            'status' => 'pending',
            'notes' => $row['notes'] ?? null,
            'valid_until' => $row['valid_until'] ?? now()->addDays(30),
        ]);
    }

    protected function createQuoteItem(Quote $quote, array $row, int $rfqId): void
    {
        // Find the request item by product name
        $requestItem = RequestItem::where('request_id', $rfqId)
            ->where('product_name', $row['product_name'])
            ->first();

        if (!$requestItem) {
            throw new \Exception("Product '{$row['product_name']}' not found in RFQ");
        }

        $quantity = (int) $row['quantity'];
        $unitPrice = (float) $row['unit_price'];
        $subtotal = $quantity * $unitPrice;

        QuoteItem::create([
            'quote_id' => $quote->id,
            'request_item_id' => $requestItem->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
        ]);

        // Update quote total
        $quote->total = $quote->items()->sum('subtotal');
        $quote->save();
    }

    public function generateTemplate(): string
    {
        $filename = 'quotes_import_template_' . date('Y-m-d_His') . '.csv';
        $path = 'templates/' . $filename;
        $fullPath = storage_path('app/' . $path);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $handle = fopen($fullPath, 'w');

        fputcsv($handle, ['rfq_id', 'product_name', 'quantity', 'unit_price', 'notes', 'valid_until']);
        fputcsv($handle, ['1', 'A4 Paper', '1000', '0.05', 'Bulk discount available', '2025-01-31']);
        fputcsv($handle, ['', 'Pens', '500', '0.25', '', '']);
        fputcsv($handle, ['2', 'Laptops', '10', '650.00', 'Next day delivery', '2025-02-15']);

        fclose($handle);

        return $path;
    }
}
