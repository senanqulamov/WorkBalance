<?php

namespace App\Livewire\Search;

use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Request;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public bool $isOpen = false;
    public array $results = [];
    public int $selectedIndex = 0;


    public function mount(): void
    {
        // Initialize empty results
        $this->results = $this->getEmptyResults();
    }

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = $this->getEmptyResults();
            $this->selectedIndex = 0;
            return;
        }

        $this->performSearch();
        $this->selectedIndex = 0;
    }

    public function open(): void
    {
        $this->isOpen = true;
        $this->query = '';
        $this->results = $this->getEmptyResults();
        $this->selectedIndex = 0;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
        $this->results = $this->getEmptyResults();
        $this->selectedIndex = 0;
    }

    protected function performSearch(): void
    {
        $query = $this->query;
        $user = auth()->user();

        if (! $user) {
            $this->results = [];
            return;
        }

        // Pull permission names once (cached in User model) to avoid repeated DB queries.
        $permissionNames = $user->cachedPermissionNames();
        $has = static fn (string $perm) => in_array('*', $permissionNames, true) || in_array($perm, $permissionNames, true);

        $this->results = [];

        // Search RFQs
        if ($has('view_rfqs')) {
            $rfqs = Request::query()
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('id', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(fn($rfq) => [
                    'type' => 'RFQ',
                    'title' => $rfq->title,
                    'subtitle' => 'RFQ #' . $rfq->id . ' - ' . $rfq->status,
                    'url' => route('rfq.show', $rfq),
                    'icon' => 'document-text',
                    'color' => 'blue',
                ]);

            if ($rfqs->isNotEmpty()) {
                $this->results['RFQs'] = $rfqs->toArray();
            }
        }

        // Search Products
        if ($has('view_products')) {
            $products = Product::query()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(fn($product) => [
                    'type' => 'Product',
                    'title' => $product->name,
                    'subtitle' => 'SKU: ' . $product->sku . ' - $' . number_format($product->price, 2),
                    'url' => route('products.show', $product),
                    'icon' => 'cube',
                    'color' => 'green',
                ]);

            if ($products->isNotEmpty()) {
                $this->results['Products'] = $products->toArray();
            }
        }

        // Search Orders
        if ($has('view_orders')) {
            $orders = Order::query()
                ->where(function ($q) use ($query) {
                    $q->where('order_number', 'like', "%{$query}%")
                        ->orWhere('status', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(fn($order) => [
                    'type' => 'Order',
                    'title' => 'Order #' . $order->order_number,
                    'subtitle' => $order->status . ' - $' . number_format($order->total_amount, 2),
                    'url' => route('orders.show', $order),
                    'icon' => 'shopping-cart',
                    'color' => 'purple',
                ]);

            if ($orders->isNotEmpty()) {
                $this->results['Orders'] = $orders->toArray();
            }
        }

        // Search Markets
        if ($has('view_markets')) {
            $markets = Market::query()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('location', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(fn($market) => [
                    'type' => 'Market',
                    'title' => $market->name,
                    'subtitle' => $market->location ? $market->location : 'Market',
                    'url' => route('markets.show', $market),
                    'icon' => 'building-storefront',
                    'color' => 'indigo',
                ]);

            if ($markets->isNotEmpty()) {
                $this->results['Markets'] = $markets->toArray();
            }
        }

        // Search Users (Admin only)
        if ($has('view_users')) {
            $users = User::query()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%")
                        ->orWhere('company_name', 'like', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(fn($u) => [
                    'type' => 'User',
                    'title' => $u->name,
                    'subtitle' => $u->email . ($u->company_name ? ' - ' . $u->company_name : ''),
                    'url' => route('users.show', $u),
                    'icon' => 'user',
                    'color' => 'red',
                ]);

            if ($users->isNotEmpty()) {
                $this->results['Users'] = $users->toArray();
            }
        }

        // Add navigation shortcuts
        $this->addNavigationShortcuts($query);
    }

    protected function addNavigationShortcuts(string $query): void
    {
        $permissionNames = auth()->user()?->cachedPermissionNames() ?? [];
        $has = static fn (string $perm) => in_array('*', $permissionNames, true) || in_array($perm, $permissionNames, true);

        $shortcuts = [
            'dashboard' => ['title' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'home'],
            'settings' => ['title' => 'Settings', 'url' => route('settings.index'), 'icon' => 'cog', 'permission' => 'view_settings'],
            'users' => ['title' => 'Users', 'url' => route('users.index'), 'icon' => 'users', 'permission' => 'view_users'],
            'products' => ['title' => 'Products', 'url' => route('products.index'), 'icon' => 'cube', 'permission' => 'view_products'],
            'orders' => ['title' => 'Orders', 'url' => route('orders.index'), 'icon' => 'shopping-cart', 'permission' => 'view_orders'],
            'markets' => ['title' => 'Markets', 'url' => route('markets.index'), 'icon' => 'building-storefront', 'permission' => 'view_markets'],
            'rfq' => ['title' => 'RFQs', 'url' => route('rfq.index'), 'icon' => 'document-text', 'permission' => 'view_rfqs'],
            'health' => ['title' => 'System Health', 'url' => route('health.index'), 'icon' => 'heart', 'permission' => 'view_health'],
            'logs' => ['title' => 'Logs', 'url' => route('logs.index'), 'icon' => 'document-duplicate', 'permission' => 'view_logs'],
        ];

        $matchedShortcuts = collect($shortcuts)
            ->filter(function ($shortcut, $key) use ($query, $has) {
                $hasPermission = ! isset($shortcut['permission']) || $has($shortcut['permission']);
                $matches = stripos($key, $query) !== false || stripos($shortcut['title'], $query) !== false;

                return $hasPermission && $matches;
            })
            ->take(3)
            ->map(fn ($shortcut) => [
                'type' => 'Navigation',
                'title' => $shortcut['title'],
                'subtitle' => 'Go to ' . $shortcut['title'],
                'url' => $shortcut['url'],
                'icon' => $shortcut['icon'],
                'color' => 'gray',
            ])
            ->values();

        if ($matchedShortcuts->isNotEmpty()) {
            $this->results['Quick Navigation'] = $matchedShortcuts->toArray();
        }
    }

    protected function getEmptyResults(): array
    {
        return [];
    }

    public function selectNext(): void
    {
        $totalItems = $this->getTotalResultsCount();
        if ($totalItems > 0) {
            $this->selectedIndex = ($this->selectedIndex + 1) % $totalItems;
        }
    }

    public function selectPrevious(): void
    {
        $totalItems = $this->getTotalResultsCount();
        if ($totalItems > 0) {
            $this->selectedIndex = ($this->selectedIndex - 1 + $totalItems) % $totalItems;
        }
    }

    public function selectCurrent(): void
    {
        $item = $this->getItemByIndex($this->selectedIndex);
        if ($item && isset($item['url'])) {
            $this->redirect($item['url']);
        }
    }

    protected function getTotalResultsCount(): int
    {
        return collect($this->results)->flatten(1)->count();
    }

    protected function getItemByIndex(int $index): ?array
    {
        $allItems = collect($this->results)->flatten(1)->values();
        return $allItems->get($index);
    }

    public function render(): View
    {
        return view('livewire.search.global-search');
    }
}
