<?php

namespace App\Services\ImportExport\Export;

use App\Models\Order;
use App\Services\ImportExport\BaseExportHandler;
use Illuminate\Support\Facades\Auth;

class OrdersExportHandler extends BaseExportHandler
{
    public function export(array $filters = []): string
    {
        $data = $this->getData($filters);
        $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';

        return $this->generateCsvFile($data, $filename);
    }

    protected function getData(array $filters): array
    {
        $query = Order::with(['items.product', 'items.market', 'user']);

        // Different query based on dashboard type
        if ($this->dashboardType === 'buyer') {
            $query->where('user_id', Auth::id());
        } elseif ($this->dashboardType === 'seller') {
            $query->whereHas('items.market', function($q) {
                $q->where('user_id', Auth::id());
            });
        } else {
            // Supplier
            $query->where('user_id', Auth::id());
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

        $orders = $query->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $data[] = [
                    $order->id,
                    $order->order_number,
                    $order->user?->name ?? '',
                    $item->product?->name ?? '',
                    $item->market?->name ?? '',
                    $item->quantity,
                    $item->unit_price,
                    $item->subtotal,
                    $order->total,
                    $order->status,
                    $order->notes ?? '',
                    $order->created_at->format('Y-m-d H:i:s'),
                ];
            }
        }

        return $data;
    }

    protected function getHeaders(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Buyer',
            'Product',
            'Market',
            'Quantity',
            'Unit Price',
            'Subtotal',
            'Total',
            'Status',
            'Notes',
            'Created At',
        ];
    }
}
