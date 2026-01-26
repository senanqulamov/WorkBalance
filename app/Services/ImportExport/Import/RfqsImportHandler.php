<?php

namespace App\Services\ImportExport\Import;

use App\Models\Request;
use App\Models\RequestItem;
use App\Services\ImportExport\BaseImportHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RfqsImportHandler extends BaseImportHandler
{
    public function import(string $filePath): array
    {
        $data = $this->readCsvFile($filePath);

        $success = 0;
        $errors = 0;
        $errorMessages = [];

        DB::beginTransaction();

        try {
            $currentRfq = null;

            foreach ($data as $index => $row) {
                try {
                    // If title is provided, start new RFQ
                    if (!empty($row['title'])) {
                        $currentRfq = $this->createRfq($row);
                        $success++;
                    }

                    // Add item to current RFQ
                    if ($currentRfq && !empty($row['product_name'])) {
                        $this->createRfqItem($currentRfq, $row);
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

    protected function createRfq(array $row): Request
    {
        return Request::create([
            'title' => $row['title'],
            'description' => $row['description'] ?? null,
            'deadline' => $row['deadline'],
            'buyer_id' => Auth::id(),
            'status' => 'draft',
        ]);
    }

    protected function createRfqItem(Request $rfq, array $row): void
    {
        RequestItem::create([
            'request_id' => $rfq->id,
            'product_name' => $row['product_name'],
            'quantity' => (int) $row['quantity'],
            'specifications' => $row['specifications'] ?? null,
        ]);
    }

    public function generateTemplate(): string
    {
        $filename = 'rfqs_import_template_' . date('Y-m-d_His') . '.csv';
        $path = 'templates/' . $filename;
        $fullPath = storage_path('app/' . $path);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $handle = fopen($fullPath, 'w');

        fputcsv($handle, ['title', 'description', 'deadline', 'product_name', 'quantity', 'specifications']);
        fputcsv($handle, ['Office Supplies Q1', 'Quarterly office supplies', '2025-03-31', 'A4 Paper', '1000', '80gsm white']);
        fputcsv($handle, ['', '', '', 'Pens', '500', 'Blue ink, ballpoint']);
        fputcsv($handle, ['IT Equipment', 'Computer equipment', '2025-04-15', 'Laptops', '10', 'Intel i5, 16GB RAM']);

        fclose($handle);

        return $path;
    }
}
