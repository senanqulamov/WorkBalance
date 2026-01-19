@props(['role' => 'default'])

@php
    $roleConfig = [
        'buyer' => [
            'gradient' => 'from-blue-600/20 to-indigo-600/20',
            'accent' => 'blue',
            'hoverBg' => 'hover:bg-blue-600/20',
            'activeBg' => 'bg-blue-600',
            'items' => [
                ['route' => 'buyer.dashboard', 'pattern' => 'buyer.dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
                ['route' => 'buyer.rfq.index', 'pattern' => 'buyer.rfq.*', 'label' => __('RFQs'), 'icon' => 'document-text', 'badge' => 5],
                ['route' => 'buyer.products.index', 'pattern' => 'buyer.products.*', 'label' => __('Products'), 'icon' => 'cube'],
                ['route' => 'buyer.markets.index', 'pattern' => 'buyer.markets.*', 'label' => __('Markets'), 'icon' => 'building-storefront'],
                ['route' => 'buyer.import-export', 'pattern' => 'buyer.import-export', 'label' => __('Import/Export'), 'icon' => 'arrow-down-tray'],
                ['route' => 'buyer.logs.index', 'pattern' => 'buyer.logs.*', 'label' => __('Activity'), 'icon' => 'clock'],
            ],
        ],
        'seller' => [
            'gradient' => 'from-emerald-600/20 to-green-600/20',
            'accent' => 'emerald',
            'hoverBg' => 'hover:bg-emerald-600/20',
            'activeBg' => 'bg-emerald-600',
            'items' => [
                ['route' => 'seller.dashboard', 'pattern' => 'seller.dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
                ['route' => 'seller.products.index', 'pattern' => 'seller.products.*', 'label' => __('Products'), 'icon' => 'cube'],
                ['route' => 'seller.orders.index', 'pattern' => 'seller.orders.*', 'label' => __('Orders'), 'icon' => 'shopping-cart', 'badge' => 12],
                ['route' => 'seller.markets.index', 'pattern' => 'seller.markets.*', 'label' => __('Markets'), 'icon' => 'building-storefront'],
                ['route' => 'seller.workers.index',  'pattern' => 'seller.workers.*', 'label' => __('Workers'), 'icon' => 'users'],
                ['route' => 'seller.import-export', 'pattern' => 'seller.import-export', 'label' => __('Import/Export'), 'icon' => 'arrow-down-tray'],
                ['route' => 'seller.logs.index', 'pattern' => 'seller.logs.*', 'label' => __('Activity'), 'icon' => 'clock'],
            ],
        ],
        'supplier' => [
            'gradient' => 'from-purple-600/20 to-indigo-600/20',
            'accent' => 'purple',
            'hoverBg' => 'hover:bg-purple-600/20',
            'activeBg' => 'bg-purple-600',
            'items' => [
                ['route' => 'supplier.dashboard', 'pattern' => 'supplier.dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
                ['route' => 'supplier.invitations.index', 'pattern' => 'supplier.invitations.*', 'label' => __('Invitations'), 'icon' => 'envelope', 'badge' => 3],
                ['route' => 'supplier.quotes.index', 'pattern' => 'supplier.quotes.*', 'label' => __('Quotes'), 'icon' => 'document-text'],
                ['route' => 'supplier.rfq.index', 'pattern' => 'supplier.rfq.*', 'label' => __('RFQs'), 'icon' => 'clipboard-document-list'],
                ['route' => 'supplier.products.index', 'pattern' => 'supplier.products.*', 'label' => __('Products'), 'icon' => 'cube'],
                ['route' => 'supplier.markets.index', 'pattern' => 'supplier.markets.*', 'label' => __('Markets'), 'icon' => 'building-storefront'],
                ['route' => 'supplier.orders.index', 'pattern' => 'supplier.orders.*', 'label' => __('Orders'), 'icon' => 'shopping-cart'],
                ['route' => 'supplier.import-export', 'pattern' => 'supplier.import-export', 'label' => __('Import/Export'), 'icon' => 'arrow-down-tray'],
                ['route' => 'supplier.logs.index', 'pattern' => 'supplier.logs.*', 'label' => __('Activity'), 'icon' => 'clock'],
            ],
        ],
        'admin' => [
            'gradient' => 'from-purple-600/20 to-indigo-600/20',
            'accent' => 'purple',
            'hoverBg' => 'hover:bg-purple-600/20',
            'activeBg' => 'bg-purple-600',
            'items' => [
                ['route' => 'dashboard', 'pattern' => 'dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
                ['route' => 'users.index', 'pattern' => 'users.*', 'label' => __('Users'), 'icon' => 'users'],
                ['route' => 'products.index', 'pattern' => 'products.*', 'label' => __('Products'), 'icon' => 'cube'],
                ['route' => 'orders.index', 'pattern' => 'orders.*', 'label' => __('Orders'), 'icon' => 'shopping-cart'],
                ['route' => 'rfq.index', 'pattern' => 'rfq.*', 'label' => __('RFQs'), 'icon' => 'document-text'],
                ['route' => 'markets.index', 'pattern' => 'markets.*', 'label' => __('Markets'), 'icon' => 'building-storefront'],
                ['route' => 'privacy.index', 'pattern' => 'privacy.*', 'label' => __('Privacy & Roles'), 'icon' => 'shield-check'],
                ['route' => 'logs.index', 'pattern' => 'logs.*', 'label' => __('Logs'), 'icon' => 'clipboard-document-list'],
                ['route' => 'notifications.index', 'pattern' => 'notifications.*', 'label' => __('Notifications'), 'icon' => 'bell'],
                ['route' => 'health.index', 'pattern' => 'health.*', 'label' => __('System Health'), 'icon' => 'heart'],
                ['route' => 'monitoring.rfq.index', 'pattern' => 'monitoring.rfq.*', 'label' => __('RFQ Monitoring'), 'icon' => 'rectangle-group'],
                ['route' => 'sla.index', 'pattern' => 'sla.*', 'label' => __('SLA Tracker'), 'icon' => 'clock'],
            ],
        ],
    ];

    $config = $roleConfig[$role] ?? $roleConfig['admin'];
@endphp

<aside
        x-data="{
        expanded: $persist(true).as('sidebar-expanded'),
        mobile: false
    }"
        x-on:sidebar-toggle.window="mobile = !mobile"
        class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300"
        x-bind:class="expanded ? 'w-64' : 'w-20'">

    {{-- Mobile Overlay --}}
    <div x-show="mobile"
         x-on:click="mobile = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="lg:hidden fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-40"></div>

    {{-- Sidebar Content --}}
    <div class="relative flex flex-col h-screen bg-slate-950/95 backdrop-blur-xl border-r border-slate-800/50 shadow-2xl lg:sticky lg:top-0"
         :class="mobile ? 'fixed inset-y-0 left-0 z-50' : 'hidden lg:flex'">

        {{-- Logo & Toggle --}}
        <div class="flex items-center justify-between p-4 border-b border-slate-800/50">
            <div class="flex items-center gap-3 w-full" x-bind:class="!expanded && 'justify-center w-full'">
                {{-- Logo: light/dark variants --}}
                <img src="{{ asset('/assets/images/fluxa_light.png') }}" class="w-fit block dark:hidden" alt="Logo light"/>
                <img src="{{ asset('/assets/images/fluxa_transparent.png') }}" class="w-fit hidden dark:block" alt="Logo dark"/>

                {{--                <span x-show="expanded"--}}
                {{--                      x-transition--}}
                {{--                      class="text-lg font-bold text-white tracking-tight">--}}
                {{--                    {{ config('app.name') }}--}}
                {{--                </span>--}}
            </div>
            <button x-on:click="expanded = !expanded"
                    class="hidden lg:block p-2 rounded-lg hover:bg-slate-800/50 transition">
                <x-icon name="chevron-left"
                        class="w-4 h-4 text-slate-400 transition-transform"
                        x-bind:class="!expanded && 'rotate-180'"/>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto p-3 space-y-1 custom-scrollbar">
            @foreach($config['items'] as $item)
                @php $active = request()->routeIs($item['pattern']); @endphp

                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                          {{ $active
                              ? $config['activeBg'] . ' text-white shadow-lg shadow-' . $config['accent'] . '-500/50'
                              : 'text-slate-400 ' . $config['hoverBg'] . ' hover:text-black hover:dark:text-white' }}"
                   x-bind:class="!expanded && 'justify-center'">

                    <div class="relative flex-shrink-0">
                        <x-icon name="{{ $item['icon'] }}"
                                class="w-5 h-5 transition-transform group-hover:scale-110"/>
                        @if(isset($item['badge']) && $item['badge'] > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-{{ $config['accent'] }}-500 rounded-full text-[10px] font-bold text-white flex items-center justify-center">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </div>

                    <span x-show="expanded"
                          x-transition
                          class="text-sm font-medium">
                        {{ $item['label'] }}
                    </span>

                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span x-show="expanded"
                              x-transition
                              class="ml-auto px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $config['accent'] }}-500 text-white">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Bottom Section --}}
        <div class="p-3 border-t border-slate-800/50 space-y-1">
            <a href="{{ auth()->user()->isAdmin() ? route('settings.index') : route('user.profile') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 {{ $config['hoverBg'] }} hover:text-black transition-all"
               x-bind:class="!expanded && 'justify-center'">
                <x-icon name="cog-6-tooth" class="w-5 h-5"/>
                <span x-show="expanded" x-transition class="text-sm font-medium">{{ __('Settings') }}</span>
            </a>
        </div>
    </div>
</aside>


<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(30, 41, 59, 0.3);
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(71, 85, 105, 0.5);
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(71, 85, 105, 0.8);
    }
</style>
