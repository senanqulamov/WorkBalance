<?php

namespace App\Services\ImportExport\Export;

use App\Models\Market;
use App\Services\ImportExport\BaseExportHandler;
use Illuminate\Support\Facades\Auth;

class MarketsExportHandler extends BaseExportHandler
{
    public function export(array $filters = []): string
    {
        $data = $this->getData($filters);
        $filename = 'markets_export_' . date('Y-m-d_His') . '.csv';

        return $this->generateCsvFile($data, $filename);
    }

    protected function getData(array $filters): array
    {
        $query = Market::where('user_id', Auth::id());

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $markets = $query->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($markets as $market) {
            $data[] = [
                $market->id,
                $market->name,
                $market->location ?? '',
                $market->description ?? '',
                $market->products()->count(),
                $market->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    protected function getHeaders(): array
    {
        return [
            'ID',
            'Name',
            'Location',
            'Description',
            'Products Count',
            'Created At',
        ];
    }
}
