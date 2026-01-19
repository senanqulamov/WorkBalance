<div class="space-y-6">
    @php
        $stats = $stats ?? [];
        $rfqStats = $rfqStats ?? [];
        $systemHealth = $systemHealth ?? [];
    @endphp

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-900 to-slate-800 border border-slate-700/60 shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
        <div class="relative p-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-slate-700/50 border border-slate-600/60 flex items-center justify-center">
                            <x-icon name="chart-bar" class="w-6 h-6 text-slate-100" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-200">Admin Dashboard</h1>
                            <div class="text-sm text-slate-300">System-wide overview and performance analytics</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="px-4 py-3 rounded-xl bg-slate-800/60 border border-slate-700/60">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Open RFQs</div>
                        <div class="text-xl font-bold text-slate-200">{{ $rfqStats['openRfqs'] ?? 0 }}</div>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-slate-800/60 border border-slate-700/60">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Pending Quotes</div>
                        <div class="text-xl font-bold text-slate-200">{{ $rfqStats['pendingQuotes'] ?? 0 }}</div>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-slate-800/60 border border-slate-700/60">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Events Today</div>
                        <div class="text-xl font-bold text-slate-200">{{ $rfqStats['eventsToday'] ?? 0 }}</div>
                    </div>
                    <div class="px-4 py-3 rounded-xl bg-slate-800/60 border border-slate-700/60">
                        <div class="text-[10px] uppercase tracking-wider text-slate-400">Health</div>
                        <div class="text-xl font-bold text-slate-200">{{ $systemHealth['score'] ?? 0 }}%</div>
                    </div>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-200/5 hover:bg-white/10 border border-slate-200/10 text-slate-200 text-sm">
                    <x-icon name="users" class="w-4 h-4" /> Users
                </a>
                <a href="{{ route('rfq.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-200/5 hover:bg-white/10 border border-slate-200/10 text-slate-200 text-sm">
                    <x-icon name="document-text" class="w-4 h-4" /> RFQs
                </a>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-200/5 hover:bg-white/10 border border-slate-200/10 text-slate-200 text-sm">
                    <x-icon name="shopping-cart" class="w-4 h-4" /> Orders
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-200/5 hover:bg-white/10 border border-slate-200/10 text-slate-200 text-sm">
                    <x-icon name="cube" class="w-4 h-4" /> Products
                </a>
                <a href="{{ route('markets.index') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-200/5 hover:bg-white/10 border border-slate-200/10 text-slate-200 text-sm">
                    <x-icon name="building-storefront" class="w-4 h-4" /> Markets
                </a>
            </div>
        </div>
    </div>

    {{-- KPI grid (from backend $stats) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5">
        @foreach($stats as $card)
            <div class="relative overflow-hidden rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-slate-400">{{ $card['label'] ?? '—' }}</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $card['count'] ?? 0 }}</div>
                            <div class="text-xs mt-1 text-gray-600 dark:text-slate-300">
                                @php $chg = (float)($card['change'] ?? 0); @endphp
                                @if($chg > 0)
                                    <span class="text-green-600">↑ {{ abs($chg) }}%</span>
                                @elseif($chg < 0)
                                    <span class="text-red-600">↓ {{ abs($chg) }}%</span>
                                @else
                                    <span class="text-slate-500">—</span>
                                @endif
                                <span> vs last period</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-slate-900/5 dark:bg-white/5 border border-gray-200/60 dark:border-slate-700/60 flex items-center justify-center">
                            <x-icon name="{{ $card['icon'] ?? 'chart-bar' }}" class="w-6 h-6 text-slate-200" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Main grid: Charts + Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Charts row --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <x-icon name="document-text" class="w-5 h-5" />
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">RFQs by status</h3>
                            </div>
                        </div>
                        <div class="h-64 relative">
                            <canvas id="dpanelChartRfqs"></canvas>
                            <div id="dpanelChartRfqsEmpty" class="absolute inset-0 hidden items-center justify-center text-sm text-gray-500">No data</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <x-icon name="document-check" class="w-5 h-5" />
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Quotes by status</h3>
                            </div>
                        </div>
                        <div class="h-64 relative">
                            <canvas id="dpanelChartQuotes"></canvas>
                            <div id="dpanelChartQuotesEmpty" class="absolute inset-0 hidden items-center justify-center text-sm text-gray-500">No data</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm xl:col-span-2">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <x-icon name="banknotes" class="w-5 h-5" />
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Sales (last 30 days)</h3>
                            </div>
                        </div>
                        <div class="h-72 relative">
                            <canvas id="dpanelChartSales"></canvas>
                            <div id="dpanelChartSalesEmpty" class="absolute inset-0 hidden items-center justify-center text-sm text-gray-500">No data</div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm xl:col-span-2">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <x-icon name="bolt" class="w-5 h-5" />
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">User activity (page views)</h3>
                            </div>
                        </div>
                        <div class="h-56 relative">
                            <canvas id="dpanelChartUsers"></canvas>
                            <div id="dpanelChartUsersEmpty" class="absolute inset-0 hidden items-center justify-center text-sm text-gray-500">No data</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent RFQs --}}
            <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="clipboard-document-list" class="w-5 h-5" />
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Recent RFQs</h3>
                        </div>
                        <a href="{{ route('rfq.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
                    </div>

                    <div class="space-y-2">
                        @forelse($recentRfqs ?? [] as $rfq)
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $rfq['title'] ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 truncate">Buyer: {{ $rfq['buyer'] ?? '—' }} • {{ $rfq['items_count'] ?? 0 }} items</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <x-badge :text="ucfirst($rfq['status'] ?? '—')" color="slate" />
                                        <div class="text-xs text-gray-500">{{ $rfq['created_at'] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No RFQs yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Operational alerts --}}
            <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="bell-alert" class="w-5 h-5" />
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Attention</h3>
                        </div>
                        <a href="{{ route('logs.index') }}" class="text-sm text-blue-600 hover:underline">Logs</a>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <span class="text-gray-600 dark:text-slate-300">Errors today</span>
                            <span class="font-semibold text-red-600">{{ $systemHealth['errorLogsToday'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <span class="text-gray-600 dark:text-slate-300">Pending quotes</span>
                            <span class="font-semibold">{{ $rfqStats['pendingQuotes'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                            <span class="text-gray-600 dark:text-slate-300">SLA reminders</span>
                            <span class="font-semibold">{{ $rfqStats['slaReminders'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="shopping-cart" class="w-5 h-5" />
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Recent Orders</h3>

                        </div>
                        <a href="{{ route('orders.index') }}" class="text-sm text-blue-600 hover:underline">View all</a>
                    </div>

                    <div class="space-y-2">
                        @forelse($recentOrders ?? [] as $order)
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-slate-800/50 border border-gray-200/60 dark:border-slate-700/60">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900 dark:text-white truncate">{{ $order['order_number'] ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ $order['product'] ?? '' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($order['total'] ?? 0, 2) }}</div>
                                        <div class="text-xs text-gray-500">{{ $order['created_at'] ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No orders yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="rounded-2xl bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-gray-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <x-icon name="clock" class="w-5 h-5" />
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Workflow / System Activity</h3>
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($workflowActivity ?? [] as $a)
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-900/5 dark:bg-white/5 border border-gray-200/60 dark:border-slate-700/60 flex items-center justify-center flex-shrink-0">
                                    <x-icon name="bolt" class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <span class="font-semibold">{{ $a['user'] ?? 'System' }}</span>
                                        <span class="text-gray-600 dark:text-slate-300"> {{ $a['description'] ?? '' }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $a['occurred_at'] ?? '' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No activity yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const data = {
                rfqs: @json($rfqsByStatus ?? []),
                quotes: @json($quotesByStatus ?? []),
                orders: @json($ordersByStatus ?? []),
                sales: @json($salesByDay ?? []),
                userActivity: @json($userActivity ?? []),
            };

            const palette = {
                open: '#22c55e',
                draft: '#f59e0b',
                closed: '#64748b',
                awarded: '#3b82f6',
                pending: '#f59e0b',
                accepted: '#22c55e',
                rejected: '#ef4444',
            };

            function el(id) { return document.getElementById(id); }

            function setEmpty(baseId, show) {
                const empty = el(baseId + 'Empty');
                const canvas = el(baseId);
                if (empty) {
                    empty.classList.toggle('hidden', !show);
                    empty.classList.toggle('flex', show);
                }
                if (canvas) canvas.classList.toggle('hidden', show);
            }

            function destroyChart(id) {
                window._dpanelCharts = window._dpanelCharts || {};
                const existing = window._dpanelCharts[id];
                if (existing && existing.destroy) {
                    try { existing.destroy(); } catch (e) {}
                }
                window._dpanelCharts[id] = null;
            }

            function doughnut(id, items) {
                const canvas = el(id);
                if (!canvas || !window.Chart) return;

                const labels = (items || []).map(x => x.label);
                const values = (items || []).map(x => Number(x.value || 0));

                if (!labels.length || values.every(v => v === 0)) {
                    setEmpty(id, true);
                    return;
                }

                setEmpty(id, false);
                destroyChart(id);

                const colors = labels.map(l => palette[String(l).toLowerCase()] || '#6366f1');

                window._dpanelCharts = window._dpanelCharts || {};
                window._dpanelCharts[id] = new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{ data: values, backgroundColor: colors, borderWidth: 0, hoverOffset: 6 }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: { enabled: true },
                        },
                        cutout: '65%'
                    }
                });
            }

            function line(id, points, valueKey, color) {
                const canvas = el(id);
                if (!canvas || !window.Chart) return;

                const labels = (points || []).map(p => p.date);
                const values = (points || []).map(p => Number(p[valueKey] || 0));

                if (!labels.length || values.every(v => v === 0)) {
                    setEmpty(id, true);
                    return;
                }

                setEmpty(id, false);
                destroyChart(id);

                window._dpanelCharts = window._dpanelCharts || {};
                window._dpanelCharts[id] = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            borderColor: color,
                            backgroundColor: color,
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 0,
                            fill: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            function renderAll() {
                doughnut('dpanelChartRfqs', data.rfqs);
                doughnut('dpanelChartQuotes', data.quotes);
                line('dpanelChartSales', data.sales, 'total', '#3b82f6');
                line('dpanelChartUsers', data.userActivity, 'count', '#22c55e');
            }

            document.addEventListener('DOMContentLoaded', renderAll);

            // Livewire safe rerender
            if (window.Livewire) {
                window.Livewire.hook('message.processed', function () {
                    renderAll();
                });
            }
        })();
    </script>
@endpush
