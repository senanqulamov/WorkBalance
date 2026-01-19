<?php

namespace App\Livewire\Shared;

use App\Livewire\Traits\Alert;
use App\Livewire\Traits\WithLogging;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;

class ImportExport extends Component
{
    use Alert, WithFileUploads, WithLogging;

    public string $dashboardType; // 'buyer', 'seller', 'supplier'
    public ?string $selectedAction = null; // 'import' or 'export'
    public ?string $selectedType = null; // 'products', 'markets', 'rfqs', 'orders', etc.
    public $uploadedFile = null;
    public bool $showInstructions = false;

    // Export filters
    public ?string $exportDateFrom = null;
    public ?string $exportDateTo = null;
    public ?string $exportStatus = null;
    public ?string $exportCategory = null;

    public function mount(): void
    {
        // Infer dashboard type from current route
        $route = request()->route()->getName();

        if (str_starts_with($route, 'buyer.')) {
            $this->dashboardType = 'buyer';
        } elseif (str_starts_with($route, 'seller.')) {
            $this->dashboardType = 'seller';
        } elseif (str_starts_with($route, 'supplier.')) {
            $this->dashboardType = 'supplier';
        } else {
            abort(404);
        }
    }

    #[Computed]
    public function availableTypes(): array
    {
        return match($this->dashboardType) {
            'buyer' => [
                'rfqs' => [
                    'label' => __('RFQs (Requests for Quotation)'),
                    'icon' => 'document-text',
                    'description' => __('Import or export your RFQs with items'),
                    'import' => true,
                    'export' => true,
                ],
                'quotes' => [
                    'label' => __('Quotes'),
                    'icon' => 'receipt-percent',
                    'description' => __('Export received quotes'),
                    'import' => false,
                    'export' => true,
                ]
            ],
            'seller' => [
                'products' => [
                    'label' => __('Products'),
                    'icon' => 'cube',
                    'description' => __('Import or export your products'),
                    'import' => true,
                    'export' => true,
                ],
                'markets' => [
                    'label' => __('Markets'),
                    'icon' => 'building-storefront',
                    'description' => __('Import or export your markets'),
                    'import' => true,
                    'export' => true,
                ],
                'orders' => [
                    'label' => __('Orders'),
                    'icon' => 'shopping-cart',
                    'description' => __('Export orders from your markets'),
                    'import' => false,
                    'export' => true,
                ],
            ],
            'supplier' => [
                'rfqs' => [
                    'label' => __('RFQs (Requests for Quotation)'),
                    'icon' => 'document-text',
                    'description' => __('Import or export your RFQs with items'),
                    'import' => false,
                    'export' => true,
                ],
                'quotes' => [
                    'label' => __('Quotes'),
                    'icon' => 'receipt-percent',
                    'description' => __('Export your submitted quotes'),
                    'import' => true,
                    'export' => true,
                ],
                'orders' => [
                    'label' => __('Orders'),
                    'icon' => 'shopping-cart',
                    'description' => __('Export your orders'),
                    'import' => true,
                    'export' => true,
                ],
            ],
            default => [],
        };
    }

    public function selectAction(string $action): void
    {
        $this->selectedAction = $action;
        $this->selectedType = null;
        $this->uploadedFile = null;
        $this->showInstructions = false;
    }

    public function selectType(string $type): void
    {
        $this->selectedType = $type;
        $this->showInstructions = true;
        $this->uploadedFile = null;
    }

    public function goBack(): void
    {
        if ($this->selectedType) {
            $this->selectedType = null;
            $this->showInstructions = false;
            $this->uploadedFile = null;
        } elseif ($this->selectedAction) {
            $this->selectedAction = null;
        }
    }

    public function getExportFilters(): array
    {
        return match($this->selectedType) {
            'products' => [
                'date' => true,
                'category' => true,
                'status' => false,
                'categories' => ['Electronics', 'Accessories', 'Furniture', 'Office Supplies', 'Other'],
            ],
            'markets' => [
                'date' => true,
                'category' => false,
                'status' => false,
            ],
            'rfqs' => [
                'date' => true,
                'category' => false,
                'status' => true,
                'statuses' => [
                    ['value' => '', 'label' => __('All Statuses')],
                    ['value' => 'draft', 'label' => __('Draft')],
                    ['value' => 'open', 'label' => __('Open')],
                    ['value' => 'closed', 'label' => __('Closed')],
                    ['value' => 'awarded', 'label' => __('Awarded')],
                    ['value' => 'cancelled', 'label' => __('Cancelled')],
                ],
            ],
            'quotes' => [
                'date' => true,
                'category' => false,
                'status' => true,
                'statuses' => [
                    ['value' => '', 'label' => __('All Statuses')],
                    ['value' => 'pending', 'label' => __('Pending')],
                    ['value' => 'accepted', 'label' => __('Accepted')],
                    ['value' => 'rejected', 'label' => __('Rejected')],
                ],
            ],
            'orders' => [
                'date' => true,
                'category' => false,
                'status' => true,
                'statuses' => [
                    ['value' => '', 'label' => __('All Statuses')],
                    ['value' => 'pending', 'label' => __('Pending')],
                    ['value' => 'accepted', 'label' => __('Accepted')],
                    ['value' => 'completed', 'label' => __('Completed')],
                    ['value' => 'cancelled', 'label' => __('Cancelled')],
                ],
            ],
            default => [
                'date' => true,
                'category' => false,
                'status' => false,
            ],
        };
    }

    public function downloadTemplate(): void
    {
        $filename = $this->generateTemplate();

        if ($filename) {
            $this->success(__('Template generated successfully'));
            $this->dispatch('download-file', url: route('download.template', ['file' => $filename]));
        } else {
            $this->error(__('Failed to generate template'));
        }
    }

    public function processImport(): void
    {
        $this->validate([
            'uploadedFile' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $handler = $this->getImportHandler();
            $result = $handler->import($this->uploadedFile->getRealPath());

            $this->logCreate(
                get_class($handler),
                0,
                [
                    'type' => $this->selectedType,
                    'dashboard' => $this->dashboardType,
                    'result' => $result,
                ]
            );

            $this->success(__('Import completed successfully. :count records imported.', ['count' => $result['success']]));

            if ($result['errors'] > 0) {
                $this->warning(__(':count records failed to import.', ['count' => $result['errors']]));
            }

            $this->uploadedFile = null;
        } catch (\Exception $e) {
            $this->error(__('Import failed: :error', ['error' => $e->getMessage()]));
        }
    }

    public function processExport(): void
    {
        try {
            $handler = $this->getExportHandler();
            $filename = $handler->export([
                'date_from' => $this->exportDateFrom,
                'date_to' => $this->exportDateTo,
                'status' => $this->exportStatus,
            ]);

            $this->logCreate(
                get_class($handler),
                0,
                [
                    'type' => $this->selectedType,
                    'dashboard' => $this->dashboardType,
                    'filters' => [
                        'date_from' => $this->exportDateFrom,
                        'date_to' => $this->exportDateTo,
                        'status' => $this->exportStatus,
                    ],
                ]
            );

            $this->success(__('Export completed successfully'));
            $this->dispatch('download-file', url: route('download.export', ['file' => $filename]));
        } catch (\Exception $e) {
            $this->error(__('Export failed: :error', ['error' => $e->getMessage()]));
        }
    }

    protected function generateTemplate(): ?string
    {
        $handler = $this->getImportHandler();
        return $handler->generateTemplate();
    }

    protected function getImportHandler()
    {
        $className = $this->getHandlerClassName('Import');

        if (!class_exists($className)) {
            throw new \Exception("Import handler not found: {$className}");
        }

        return new $className($this->dashboardType);
    }

    protected function getExportHandler()
    {
        $className = $this->getHandlerClassName('Export');

        if (!class_exists($className)) {
            throw new \Exception("Export handler not found: {$className}");
        }

        return new $className($this->dashboardType);
    }

    protected function getHandlerClassName(string $action): string
    {
        $type = ucfirst($this->selectedType);
        return "App\\Services\\ImportExport\\{$action}\\{$type}{$action}Handler";
    }

    public function getInstructions(): array
    {
        if (!$this->selectedAction || !$this->selectedType) {
            return [];
        }

        return $this->getInstructionsForType($this->selectedAction, $this->selectedType);
    }

    protected function getInstructionsForType(string $action, string $type): array
    {
        $instructions = [
            'import' => [
                'rfqs' => [
                    'title' => __('Import RFQs'),
                    'description' => __('Import multiple RFQs with their items from a CSV/Excel file'),
                    'steps' => [
                        __('Download the template file'),
                        __('Fill in your RFQ data following the example'),
                        __('Save the file as CSV or Excel'),
                        __('Upload the file using the form below'),
                    ],
                    'fields' => [
                        ['name' => 'title', 'required' => true, 'description' => __('RFQ title')],
                        ['name' => 'description', 'required' => false, 'description' => __('Detailed description')],
                        ['name' => 'deadline', 'required' => true, 'description' => __('Deadline date (YYYY-MM-DD)')],
                        ['name' => 'product_name', 'required' => true, 'description' => __('Product name')],
                        ['name' => 'quantity', 'required' => true, 'description' => __('Quantity needed')],
                        ['name' => 'specifications', 'required' => false, 'description' => __('Product specifications')],
                    ],
                    'example' => [
                        ['title' => 'Office Supplies Q1', 'description' => 'Quarterly office supplies', 'deadline' => '2025-03-31', 'product_name' => 'A4 Paper', 'quantity' => '1000', 'specifications' => '80gsm white'],
                        ['title' => 'Office Supplies Q1', 'description' => 'Quarterly office supplies', 'deadline' => '2025-03-31', 'product_name' => 'Pens', 'quantity' => '500', 'specifications' => 'Blue ink, ballpoint'],
                    ],
                ],
                'products' => [
                    'title' => __('Import Products'),
                    'description' => __('Import multiple products from a CSV/Excel file'),
                    'steps' => [
                        __('Download the template file'),
                        __('Fill in your product data'),
                        __('Ensure SKUs are unique'),
                        __('Upload the file'),
                    ],
                    'fields' => [
                        ['name' => 'name', 'required' => true, 'description' => __('Product name')],
                        ['name' => 'sku', 'required' => true, 'description' => __('Unique SKU code')],
                        ['name' => 'price', 'required' => true, 'description' => __('Product price')],
                        ['name' => 'stock', 'required' => true, 'description' => __('Stock quantity')],
                        ['name' => 'category', 'required' => false, 'description' => __('Product category')],
                        ['name' => 'market_name', 'required' => true, 'description' => __('Market name')],
                    ],
                    'example' => [
                        ['name' => 'Laptop HP 15', 'sku' => 'LAP-HP-001', 'price' => '599.99', 'stock' => '50', 'category' => 'Electronics', 'market_name' => 'Tech Market'],
                        ['name' => 'Mouse Wireless', 'sku' => 'MOU-WL-001', 'price' => '19.99', 'stock' => '200', 'category' => 'Accessories', 'market_name' => 'Tech Market'],
                    ],
                ],
                'markets' => [
                    'title' => __('Import Markets'),
                    'description' => __('Import multiple markets from a CSV/Excel file'),
                    'steps' => [
                        __('Download the template file'),
                        __('Fill in your market data'),
                        __('Upload the file'),
                    ],
                    'fields' => [
                        ['name' => 'name', 'required' => true, 'description' => __('Market name')],
                        ['name' => 'location', 'required' => false, 'description' => __('Market location')],
                        ['name' => 'description', 'required' => false, 'description' => __('Market description')],
                    ],
                    'example' => [
                        ['name' => 'Tech Market', 'location' => 'New York', 'description' => 'Electronics and gadgets'],
                        ['name' => 'Food Market', 'location' => 'Los Angeles', 'description' => 'Fresh produce and groceries'],
                    ],
                ],
                'quotes' => [
                    'title' => __('Import Quotes'),
                    'description' => __('Import quotes for RFQs from a CSV file'),
                    'steps' => [
                        __('Download the template file'),
                        __('Fill in quote data for each RFQ'),
                        __('Ensure RFQ IDs are valid'),
                        __('Upload the file'),
                    ],
                    'fields' => [
                        ['name' => 'rfq_id', 'required' => true, 'description' => __('RFQ ID to quote for')],
                        ['name' => 'product_name', 'required' => true, 'description' => __('Product name from RFQ')],
                        ['name' => 'quantity', 'required' => true, 'description' => __('Quantity to supply')],
                        ['name' => 'unit_price', 'required' => true, 'description' => __('Price per unit')],
                        ['name' => 'notes', 'required' => false, 'description' => __('Additional notes')],
                        ['name' => 'valid_until', 'required' => false, 'description' => __('Quote validity date (YYYY-MM-DD)')],
                    ],
                    'example' => [
                        ['rfq_id' => '1', 'product_name' => 'A4 Paper', 'quantity' => '1000', 'unit_price' => '0.05', 'notes' => 'Bulk discount available', 'valid_until' => '2025-01-31'],
                        ['rfq_id' => '', 'product_name' => 'Pens', 'quantity' => '500', 'unit_price' => '0.25', 'notes' => '', 'valid_until' => ''],
                    ],
                ],
                'orders' => [
                    'title' => __('Import Orders'),
                    'description' => __('Import orders with items from a CSV file'),
                    'steps' => [
                        __('Download the template file'),
                        __('Fill in order data'),
                        __('Ensure products and markets exist'),
                        __('Upload the file'),
                    ],
                    'fields' => [
                        ['name' => 'order_number', 'required' => true, 'description' => __('Unique order number')],
                        ['name' => 'product_name', 'required' => true, 'description' => __('Product name')],
                        ['name' => 'market_name', 'required' => true, 'description' => __('Market name')],
                        ['name' => 'quantity', 'required' => true, 'description' => __('Quantity to order')],
                        ['name' => 'unit_price', 'required' => true, 'description' => __('Price per unit')],
                        ['name' => 'status', 'required' => false, 'description' => __('Order status (pending, accepted, etc.)')],
                        ['name' => 'notes', 'required' => false, 'description' => __('Order notes')],
                    ],
                    'example' => [
                        ['order_number' => 'ORD-2025-001', 'product_name' => 'Laptop HP 15', 'market_name' => 'Tech Market', 'quantity' => '2', 'unit_price' => '599.99', 'status' => 'pending', 'notes' => 'Rush order'],
                        ['order_number' => '', 'product_name' => 'Mouse Wireless', 'market_name' => 'Tech Market', 'quantity' => '5', 'unit_price' => '19.99', 'status' => '', 'notes' => ''],
                    ],
                ],
            ],
            'export' => [
                'rfqs' => [
                    'title' => __('Export RFQs'),
                    'description' => __('Export your RFQs with all items to CSV/Excel format'),
                    'info' => __('The export will include all RFQ details, items, and their specifications.'),
                ],
                'products' => [
                    'title' => __('Export Products'),
                    'description' => __('Export your products to CSV/Excel format'),
                    'info' => __('The export will include all product details, pricing, and stock information.'),
                ],
                'markets' => [
                    'title' => __('Export Markets'),
                    'description' => __('Export your markets to CSV/Excel format'),
                    'info' => __('The export will include market details and statistics.'),
                ],
                'quotes' => [
                    'title' => __('Export Quotes'),
                    'description' => __('Export quotes to CSV/Excel format'),
                    'info' => __('The export will include quote details, items, and pricing.'),
                ],
                'orders' => [
                    'title' => __('Export Orders'),
                    'description' => __('Export orders to CSV/Excel format'),
                    'info' => __('The export will include order details, items, and totals.'),
                ],
            ],
        ];

        return $instructions[$action][$type] ?? [];
    }

    public function render(): View
    {
        return view('livewire.shared.import-export')
            ->layout('layouts.app');
    }
}
