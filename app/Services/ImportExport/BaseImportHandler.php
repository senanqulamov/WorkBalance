<?php

namespace App\Services\ImportExport;

abstract class BaseImportHandler
{
    protected string $dashboardType;

    public function __construct(string $dashboardType)
    {
        $this->dashboardType = $dashboardType;
    }

    abstract public function import(string $filePath): array;

    abstract public function generateTemplate(): string;

    protected function readCsvFile(string $filePath): array
    {
        $data = [];
        $handle = fopen($filePath, 'r');

        if ($handle) {
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }

            fclose($handle);
        }

        return $data;
    }

    protected function detectFileType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match($extension) {
            'csv' => 'csv',
            default => throw new \Exception('Only CSV files are supported currently'),
        };
    }
}
