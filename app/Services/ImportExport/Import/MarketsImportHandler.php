<?php

namespace App\Services\ImportExport\Import;

use App\Models\Market;
use App\Services\ImportExport\BaseImportHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketsImportHandler extends BaseImportHandler
{
    public function import(string $filePath): array
    {
        $data = $this->readCsvFile($filePath);

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
        // Check if market already exists
        $existing = Market::where('name', $row['name'])
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            throw new \Exception("Market '{$row['name']}' already exists");
        }

        Market::create([
            'name' => $row['name'],
            'location' => $row['location'] ?? null,
            'description' => $row['description'] ?? null,
            'user_id' => Auth::id(),
        ]);
    }

    public function generateTemplate(): string
    {
        $filename = 'markets_import_template_' . date('Y-m-d_His') . '.csv';
        $path = 'templates/' . $filename;
        $fullPath = storage_path('app/' . $path);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $handle = fopen($fullPath, 'w');

        fputcsv($handle, ['name', 'location', 'description']);
        fputcsv($handle, ['Tech Market', 'New York', 'Electronics and gadgets']);
        fputcsv($handle, ['Food Market', 'Los Angeles', 'Fresh produce and groceries']);

        fclose($handle);

        return $path;
    }
}
