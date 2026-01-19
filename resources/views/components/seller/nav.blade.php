@php
    $items = [
        ['route' => 'seller.dashboard',      'pattern' => 'seller.dashboard',      'label' => __('Overview'),     'icon' => 'home-modern'],
        ['route' => 'seller.products.index', 'pattern' => 'seller.products.*',     'label' => __('Products'),     'icon' => 'cube'],
        ['route' => 'seller.orders.index',   'pattern' => 'seller.orders.*',       'label' => __('Orders'),       'icon' => 'receipt-percent'],
        ['route' => 'seller.markets.index',  'pattern' => 'seller.markets.*',      'label' => __('Markets'),      'icon' => 'building-storefront'],
        ['route' => 'seller.workers.index',  'pattern' => 'seller.workers.*',      'label' => __('Workers'),      'icon' => 'users'],
        ['route' => 'seller.logs.index',     'pattern' => 'seller.logs.*',         'label' => __('Activity Log'), 'icon' => 'clipboard-document-list'],
        ['route' => 'settings.index',        'pattern' => 'settings.*',            'label' => __('Settings'),     'icon' => 'cog-6-tooth'],
        ['route' => 'seller.import-export', 'pattern' => 'seller.import-export', 'label' => __('Import/Export'), 'icon' => 'arrows-up-down'],
    ];
@endphp

<div class="sticky top-20 z-50 -mx-6 -mt-6 mb-6 px-6 py-4 backdrop-blur-sm shadow-lg rounded-full">
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($items as $item)
            @php $active = request()->routeIs($item['pattern']); @endphp

            <a href="{{ route($item['route']) }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition
                      {{ $active
                          ? 'bg-emerald-600 text-white shadow-sm'
                          : 'bg-slate-900/70 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700' }}">
                <x-icon name="{{ $item['icon'] }}"
                        class="w-4 h-4 {{ $active ? 'text-white' : 'text-emerald-400' }}" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
