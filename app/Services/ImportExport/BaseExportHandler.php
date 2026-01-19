<?php

namespace App\Services\ImportExport;

use Illuminate\Support\Facades\Storage;

abstract class BaseExportHandler
{
    protected string $dashboardType;

    public function __construct(string $dashboardType)
    {
        $this->dashboardType = $dashboardType;
    }

    abstract public function export(array $filters = []): string;

    abstract protected function getData(array $filters): array;

    abstract protected function getHeaders(): array;

    protected function generateCsvFile(array $data, string $filename): string
    {
        $path = 'exports/' . $filename;
        $fullPath = storage_path('app/' . $path);

        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $handle = fopen($fullPath, 'w');

        // Write headers
        fputcsv($handle, $this->getHeaders());

        // Write data
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $path;
    }
}
