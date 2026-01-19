<?php

namespace App\Services\ImportExport\Import;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Market;
use App\Services\ImportExport\BaseImportHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersImportHandler extends BaseImportHandler
{
    public function import(string $filePath): array
    {
        $data = $this->readCsvFile($filePath);

        $success = 0;
        $errors = 0;
        $errorMessages = [];

        DB::beginTransaction();

        try {
            $currentOrder = null;

            foreach ($data as $index => $row) {
                try {
                    // If order_number is provided, start new order
                    if (!empty($row['order_number'])) {
                        $currentOrder = $this->createOrder($row);
                        $success++;
                    }

                    // Add item to current order
                    if ($currentOrder && !empty($row['product_name'])) {
                        $this->createOrderItem($currentOrder, $row);
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

    protected function createOrder(array $row): Order
    {
        return Order::create([
            'order_number' => $row['order_number'],
            'user_id' => Auth::id(),
            'total' => 0, // Will be calculated when items are added
            'status' => $row['status'] ?? 'pending',
            'notes' => $row['notes'] ?? null,
        ]);
    }

    protected function createOrderItem(Order $order, array $row): void
    {
        // Find market by name
        $market = Market::where('name', $row['market_name'])->first();
        if (!$market) {
            throw new \Exception("Market '{$row['market_name']}' not found");
        }

        // Find product by name and market
        $product = Product::where('name', $row['product_name'])
            ->where('market_id', $market->id)
            ->first();

        if (!$product) {
            throw new \Exception("Product '{$row['product_name']}' not found in market '{$row['market_name']}'");
        }

        $quantity = (int) $row['quantity'];
        $unitPrice = (float) $row['unit_price'];
        $subtotal = $quantity * $unitPrice;

        // Check stock
        if ($product->stock < $quantity) {
            throw new \Exception("Insufficient stock for '{$row['product_name']}'. Available: {$product->stock}, Requested: {$quantity}");
        }

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'market_id' => $market->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
        ]);

        // Update product stock
        $product->stock -= $quantity;
        $product->save();

        // Update order total
        $order->total = $order->items()->sum('subtotal');
        $order->save();
    }

    public function generateTemplate(): string
    {
        $filename = 'orders_import_template_' . date('Y-m-d_His') . '.csv';
        $path = 'templates/' . $filename;
        $fullPath = storage_path('app/' . $path);

        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        $handle = fopen($fullPath, 'w');

        fputcsv($handle, ['order_number', 'product_name', 'market_name', 'quantity', 'unit_price', 'status', 'notes']);
        fputcsv($handle, ['ORD-2025-001', 'Laptop HP 15', 'Tech Market', '2', '599.99', 'pending', 'Rush order']);
        fputcsv($handle, ['', 'Mouse Wireless', 'Tech Market', '5', '19.99', '', '']);
        fputcsv($handle, ['ORD-2025-002', 'Office Chair', 'Furniture Market', '10', '150.00', 'pending', 'Bulk order discount']);

        fclose($handle);

        return $path;
    }
}
