<div class="space-y-6">
    @php
        $roleNames = $user->roles()->pluck('name')->all();
        $isBuyer = in_array('buyer', $roleNames, true) || $user->is_buyer;
        $isSeller = in_array('seller', $roleNames, true) || $user->is_seller;
        $isSupplier = in_array('supplier', $roleNames, true) || $user->is_supplier;
        $isAdmin = in_array('admin', $roleNames, true) || $user->is_admin;
        $isWorker = in_array('market_worker', $roleNames, true);
    @endphp

    {{-- Header / Hero --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 border border-slate-700/60 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
        <div class="relative p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-700/50 flex items-center justify-center border border-slate-600/60">
                            <x-icon name="user" class="w-6 h-6 text-slate-100" />
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-2xl font-bold text-slate-200 truncate">
                                {{ $user->name }}
                            </h1>
                            <div class="text-sm text-slate-300 truncate">{{ $user->email }}</div>
                            @if($user->company_name)
                                <div class="text-xs text-slate-400 truncate">{{ $user->company_name }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 mt-4">
                        @if($isBuyer)
                            <x-badge color="blue" text="Buyer" icon="shopping-cart" position="left" />
                        @endif
                        @if($isSeller)
                            <x-badge color="green" text="{{ $user->verified_seller ? 'Verified Seller' : 'Seller' }}" icon="building-storefront" position="left" />
                        @endif
                        @if($isSupplier)
                            @if($user->supplier_status === 'active')
                                <x-badge color="purple" text="Active Supplier" icon="cube" position="left" />
                            @else
                                <x-badge color="slate" text="Supplier ({{ ucfirst($user->supplier_status) }})" icon="cube" position="left" />
                            @endif
                        @endif
                        @if($isAdmin)
                            <x-badge color="slate" text="Admin" icon="shield-check" position="left" />
                        @endif
                        @if($isWorker)
                            <x-badge color="slate" text="Market Worker" icon="users" position="left" />
                        @endif

                        <x-badge :color="$user->is_active ? 'green' : 'red'" :text="$user->is_active ? 'Active' : 'Inactive'" icon="power" position="left" />
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-button icon="arrow-left" href="{{ route('users.index') }}">Back</x-button>

                    @if($isSupplier)
                        @if($user->supplier_status === 'pending')
                            <x-button icon="check-circle" color="green" wire:click="$dispatch('load::supplier::approve', { 'supplier' : '{{ $user->id }}' })">Approve</x-button>
                            <x-button icon="x-circle" color="red" wire:click="$dispatch('load::supplier::reject', { 'supplier' : '{{ $user->id }}' })">Reject</x-button>
                        @elseif($user->supplier_status === 'active')
                            <x-button icon="shield-exclamation" color="orange" wire:click="$dispatch('load::supplier::block', { 'supplier' : '{{ $user->id }}' })">Block</x-button>
                        @elseif(in_array($user->supplier_status, ['blocked', 'inactive']))
                            <x-button icon="arrow-path" color="green" wire:click="$dispatch('load::supplier::reactivate', { 'supplier' : '{{ $user->id }}' })">Reactivate</x-button>
                        @endif
                    @endif

                    <x-button icon="pencil" wire:click="$dispatch('load::user', { user: '{{ $user->id }}' })">Edit</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Role-specific sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left column --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Supplier --}}
            @if($isSupplier)
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="cube" class="w-5 h-5" />
                            <h2 class="text-lg font-semibold">Supplier Overview</h2>
                        </div>
                        <x-badge :text="ucfirst($user->supplier_status)" :color="$user->supplier_status === 'active' ? 'green' : ($user->supplier_status === 'pending' ? 'yellow' : 'slate')" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <div class="text-xs text-gray-500">Supplier Code</div>
                            <div class="font-semibold text-gray-900 dark:text-white font-mono">{{ $user->supplier_code ?? '—' }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <div class="text-xs text-gray-500">Ariba Network ID</div>
                            <div class="font-semibold text-gray-900 dark:text-white font-mono">{{ $user->ariba_network_id ?? '—' }}</div>
                        </div>
                    </div>

                    @if($this->suppliedProducts->isNotEmpty())
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Supplied Products</h3>
                                <x-badge color="slate" :text="$this->suppliedProducts->count() . ' items'" sm />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($this->suppliedProducts->take(6) as $product)
                                    <a href="{{ route('products.show', $product) }}" class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60 hover:bg-gray-100 dark:hover:bg-slate-800 transition">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                        <div class="text-xs text-gray-500 mt-1">SKU: <span class="font-mono">{{ $product->sku }}</span></div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-card>
            @endif

            {{-- Seller --}}
            @if($isSeller)
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="building-storefront" class="w-5 h-5" />
                            <h2 class="text-lg font-semibold">{{ __('Seller Overview') }}</h2>
                        </div>
                        <x-badge :color="$user->verified_seller ? 'green' : 'slate'" :text="$user->verified_seller ? 'Verified' : 'Not Verified'" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-stat label="Commission" :value="($user->commission_rate !== null ? number_format($user->commission_rate, 2) . '%' : '—')" icon="percent-badge" />
                        <x-stat label="Markets" :value="$user->markets()->count()" icon="building-storefront" />
                        <x-stat label="Workers" :value="$user->workers()->count()" icon="users" />
                    </div>

                    <div class="mt-5">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Owned Markets</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->markets()->limit(10)->get(['id','name']) as $market)
                                <a href="{{ route('markets.show', $market) }}">
                                    <x-badge :text="$market->name" icon="building-storefront" position="left" />
                                </a>
                            @endforeach
                            @if($user->markets()->count() === 0)
                                <div class="text-sm text-gray-500">No markets yet.</div>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endif

            {{-- Buyer --}}
            @if($isBuyer)
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <x-icon name="shopping-cart" class="w-5 h-5" />
                        <h2 class="text-lg font-semibold">Buyer Overview</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <div class="text-xs text-gray-500">Company</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $user->company_name ?? '—' }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <div class="text-xs text-gray-500">Tax ID</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $user->tax_id ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                        <x-stat label="Orders" :value="$this->metrics['orders_count']" icon="shopping-bag" />
                        <x-stat label="Lifetime" :value="'$' . number_format($this->metrics['lifetime_value'], 2)" icon="banknotes" />
                        <x-stat label="Avg Order" :value="'$' . number_format($this->metrics['avg_order_value'], 2)" icon="chart-bar" />
                        <x-stat label="Markets" :value="$this->metrics['markets_count']" icon="building-storefront" />
                    </div>
                </x-card>
            @endif

            {{-- Worker --}}
            @if($isWorker)
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <x-icon name="users" class="w-5 h-5" />
                        <h2 class="text-lg font-semibold">Market Worker Assignment</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <div class="text-xs text-gray-500">Seller Owner</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $user->seller?->name ?? '—' }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <div class="text-xs text-gray-500">Assigned Markets</div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($user->workerMarkets()->get(['markets.id','markets.name']) as $m)
                                    <x-badge :text="$m->name" color="slate" sm />
                                @endforeach
                                @if($user->workerMarkets()->count() === 0)
                                    <span class="text-sm text-gray-500">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </x-card>
            @endif

            {{-- Recent Orders (shared) --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Recent Orders</h2>
                </div>

                <x-table :headers="[
                    ['index' => 'order_number', 'label' => 'Order Number'],
                    ['index' => 'total', 'label' => 'Total'],
                    ['index' => 'status', 'label' => 'Status'],
                    ['index' => 'created_at', 'label' => 'Created'],
                ]" :rows="$this->orders" paginate :paginator="null" loading>
                    @interact('column_order_number', $row)
                        <a href="{{ route('orders.show', $row) }}" class="text-blue-600 hover:underline">
                            <x-badge text="{{ $row->order_number }}" icon="queue-list" position="left" />
                        </a>
                    @endinteract

                    @interact('column_total', $row)
                        ${{ number_format($row->total, 2) }}
                    @endinteract

                    @interact('column_status', $row)
                        <x-badge :text="ucfirst($row->status)" />
                    @endinteract

                    @interact('column_created_at', $row)
                        {{ $row->created_at->diffForHumans() }}
                    @endinteract
                </x-table>
            </x-card>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Contact</h2>
                    @if($user->hasCompleteAddress())
                        <x-badge color="green" text="Address set" sm />
                    @else
                        <x-badge color="slate" text="Incomplete" sm />
                    @endif
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Phone</span>
                        <span class="font-medium">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Mobile</span>
                        <span class="font-medium">{{ $user->mobile ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Website</span>
                        <span class="font-medium truncate max-w-[160px]">{{ $user->website ?? '—' }}</span>
                    </div>

                    <div class="pt-3 border-t border-gray-200 dark:border-slate-700">
                        <div class="text-gray-500 mb-1">Address</div>
                        <div class="text-gray-900 dark:text-gray-100 text-sm">{{ $user->hasCompleteAddress() ? $user->getFullAddress() : '—' }}</div>
                    </div>
                </div>
            </x-card>

            <x-card>
                <h2 class="text-lg font-semibold mb-4">Account</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                        <div class="text-xs text-gray-500">Created</div>
                        <div class="font-semibold">{{ optional($user->created_at)->diffForHumans() }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                        <div class="text-xs text-gray-500">Email Verified</div>
                        <div class="font-semibold">{{ $user->email_verified_at ? 'Yes' : 'No' }}</div>
                    </div>
                </div>

                @if($user->notes)
                    <div class="mt-4">
                        <div class="text-xs text-gray-500 mb-1">Internal Notes</div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $user->notes }}</div>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    @if($isSupplier)
        <livewire:users.supplier-actions wire:key="supplier-actions-{{ $user->id }}" />
    @endif

    <livewire:users.update @updated="$refresh" />
</div>
