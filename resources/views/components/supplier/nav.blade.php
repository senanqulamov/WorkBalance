@php
    $items = [
        ['route' => 'supplier.dashboard',          'pattern' => 'supplier.dashboard',          'label' => __('Overview'),     'icon' => 'home-modern'],
        ['route' => 'supplier.invitations.index',  'pattern' => 'supplier.invitations.*',      'label' => __('Invitations'), 'icon' => 'envelope'],
        ['route' => 'supplier.quotes.index',       'pattern' => 'supplier.quotes.*',           'label' => __('Quotes'),       'icon' => 'document-text'],
        ['route' => 'supplier.rfq.index',          'pattern' => 'supplier.rfq.*',              'label' => __('RFQs'),         'icon' => 'clipboard-document-list'],
        ['route' => 'supplier.products.index',     'pattern' => 'supplier.products.*',         'label' => __('Products'),     'icon' => 'cube'],
        ['route' => 'supplier.markets.index',      'pattern' => 'supplier.markets.*',          'label' => __('Markets'),      'icon' => 'building-storefront'],
        ['route' => 'supplier.orders.index',       'pattern' => 'supplier.orders.*',           'label' => __('Orders'),       'icon' => 'shopping-cart'],
        ['route' => 'supplier.logs.index',         'pattern' => 'supplier.logs.*',             'label' => __('Activity Log'), 'icon' => 'clipboard-document-list'],
        ['route' => 'settings.index',              'pattern' => 'settings.*',                  'label' => __('Settings'),     'icon' => 'cog-6-tooth'],
        ['route' => 'supplier.import-export',      'pattern' => 'supplier.import-export',      'label' => __('Import/Export'), 'icon' => 'arrows-up-down'],
    ];
@endphp

<div class="sticky top-20 z-50 -mx-6 -mt-6 mb-6 px-6 py-4 backdrop-blur-sm shadow-lg rounded-full">
    <div class="flex flex-wrap items-center justify-center gap-2">
        @foreach ($items as $item)
            @php $active = request()->routeIs($item['pattern']); @endphp

            <a href="{{ route($item['route']) }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition
                      {{ $active
                          ? 'bg-purple-600 text-white shadow-sm'
                          : 'bg-slate-900/70 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-700' }}">
                <x-icon name="{{ $item['icon'] }}"
                        class="w-4 h-4 {{ $active ? 'text-white' : 'text-purple-400' }}" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
