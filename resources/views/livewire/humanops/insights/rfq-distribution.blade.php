<div>
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center py-6">
            <div>
                <table class="min-w-full bg-slate-900 rounded-lg overflow-hidden shadow">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ __('Count') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($data as $key => $value)
                            <tr>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-opacity-20 {{
                                        $key === 'open' ? 'bg-blue-500 text-blue-400' :
                                        ($key === 'due_3_days' ? 'bg-amber-500 text-amber-400' :
                                        ($key === 'overdue' ? 'bg-red-500 text-red-400' :
                                        ($key === 'closed' ? 'bg-emerald-500 text-emerald-400' : 'bg-slate-500 text-slate-400')))
                                    }}">
                                        {{ __(str_replace('_', ' ', ucfirst($key))) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right font-semibold">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <canvas id="rfqDistributionChart" height="220"
                        data-labels='@json($labels)'
                        data-values='@json(array_values($data))'></canvas>
            </div>
        </div>
    </x-card>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function initRfqChart() {
            const el = document.getElementById('rfqDistributionChart');
            if (!el) return;
            // Read data from data- attributes so re-initialization after Livewire updates works
            let labels = [];
            let values = [];
            try {
                labels = JSON.parse(el.dataset.labels || '[]');
                values = JSON.parse(el.dataset.values || '[]');
            } catch (e) {
                console.error('Invalid chart data', e);
            }

            // Debug: log parsed data so developers can inspect values in the browser console
            try {
                console.debug('rfqDistributionChart - labels:', labels, 'values:', values, 'ChartJS:', typeof Chart !== 'undefined');
            } catch (e) { /* ignore in older browsers */ }

            const ctx = el.getContext('2d');
            if (window.rfqDistributionChart) {
                try { window.rfqDistributionChart.destroy(); } catch (e) { /* ignore */ }
            }

            window.rfqDistributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#2563eb', // blue
                            '#64748b', // slate
                            '#10b981', // emerald
                            '#f59e42', // amber
                            '#ef4444', // red
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        legend: { display: true, position: 'bottom' }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        document.addEventListener('livewire:load', function () {
            initRfqChart();

            // Re-init chart after Livewire updates (so canvas/labels updated by server render are picked up)
            if (window.Livewire && typeof Livewire.hook === 'function') {
                Livewire.hook('message.processed', function () {
                    initRfqChart();
                });
            }
        });
    </script>
</div>
