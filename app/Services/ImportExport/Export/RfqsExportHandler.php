<?php

namespace App\Services\ImportExport\Export;

use App\Models\Request;
use App\Services\ImportExport\BaseExportHandler;
use Illuminate\Support\Facades\Auth;

class RfqsExportHandler extends BaseExportHandler
{
    public function export(array $filters = []): string
    {
        $data = $this->getData($filters);
        $filename = 'rfqs_export_' . date('Y-m-d_His') . '.csv';

        return $this->generateCsvFile($data, $filename);
    }

    protected function getData(array $filters): array
    {
        $query = Request::with('items')->where('buyer_id', Auth::id());

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $rfqs = $query->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($rfqs as $rfq) {
            foreach ($rfq->items as $item) {
                $data[] = [
                    $rfq->id,
                    $rfq->title,
                    $rfq->description ?? '',
                    $rfq->deadline->format('Y-m-d'),
                    $rfq->status,
                    $item->product_name,
                    $item->quantity,
                    $item->specifications ?? '',
                    $rfq->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $data;
    }

    protected function getHeaders(): array
    {
        return [
            'RFQ ID',
            'Title',
            'Description',
            'Deadline',
            'Status',
            'Product Name',
            'Quantity',
            'Specifications',
            'Created At',
        ];
    }
}
