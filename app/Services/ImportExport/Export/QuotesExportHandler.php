<?php

namespace App\Services\ImportExport\Export;

use App\Models\Quote;
use App\Services\ImportExport\BaseExportHandler;
use Illuminate\Support\Facades\Auth;

class QuotesExportHandler extends BaseExportHandler
{
    public function export(array $filters = []): string
    {
        $data = $this->getData($filters);
        $filename = 'quotes_export_' . date('Y-m-d_His') . '.csv';

        return $this->generateCsvFile($data, $filename);
    }

    protected function getData(array $filters): array
    {
        $query = Quote::with(['request', 'items.requestItem']);

        // Different query based on dashboard type
        if ($this->dashboardType === 'buyer') {
            $query->whereHas('request', function($q) {
                $q->where('buyer_id', Auth::id());
            });
        } else {
            // Supplier
            $query->where('supplier_id', Auth::id());
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $quotes = $query->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($quotes as $quote) {
            foreach ($quote->items as $item) {
                $data[] = [
                    $quote->id,
                    $quote->request?->title ?? '',
                    $quote->status,
                    $item->requestItem?->product_name ?? '',
                    $item->quantity,
                    $item->unit_price,
                    $item->subtotal,
                    $quote->total,
                    $quote->notes ?? '',
                    $quote->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $data;
    }

    protected function getHeaders(): array
    {
        return [
            'Quote ID',
            'RFQ Title',
            'Status',
            'Product Name',
            'Quantity',
            'Unit Price',
            'Subtotal',
            'Total',
            'Notes',
            'Created At',
        ];
    }
}
