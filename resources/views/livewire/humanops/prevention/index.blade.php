<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold mr-3 text-slate-200">{{ __('Prevention Center') }}</h3>
                    <p class="text-sm text-slate-400">{{ __('Review aggregated risk signals and act with care. No individual data is shown.') }}</p>
                </div>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card>
                <x-slot name="header">
                    <div class="text-slate-200">{{ __('Active Risk Signals') }}</div>
                </x-slot>

                <x-table :headers="$signalHeaders" :rows="$this->signals">
                    @interact('column_action', $row)
                        <x-button sm wire:click="openSignal('{{ $row['type_key'] }}', {{ $row['id'] }})">
                            {{ __('View') }}
                        </x-button>
                    @endinteract
                </x-table>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <div class="text-slate-200">{{ __('Top Recommendations') }}</div>
                </x-slot>

                <x-table :headers="$recommendationHeaders" :rows="$this->recommendations">
                    @interact('column_action', $row)
                        <x-button sm wire:click="openRecommendation({{ $row['id'] }})">
                            {{ __('View') }}
                        </x-button>
                    @endinteract
                </x-table>
            </x-card>
        </div>

        <div class="mt-6">
            <x-alert icon="shield-check" color="info">
                {{ __('Privacy reminder: leaders see trends and signals only after aggregation, anonymization, and delay rules are applied.') }}
            </x-alert>
        </div>
    </x-card>

    {{-- Signal Slide --}}
    <x-slide wire:model="slideSignal" title="{{ __('Risk Signal') }}" size="lg">
        @if($selectedSignal)
            <div class="space-y-4">
                <div class="text-sm text-slate-400">
                    <div><strong class="text-slate-200">{{ __('Department') }}:</strong> {{ $selectedSignal['department'] }}</div>
                    <div><strong class="text-slate-200">{{ __('Severity') }}:</strong> {{ ucfirst($selectedSignal['severity']) }}</div>
                    <div><strong class="text-slate-200">{{ __('Detected') }}:</strong> {{ $selectedSignal['detected_at'] ?? '—' }}</div>
                </div>

                @if(!empty($selectedSignal['description']))
                    <x-alert icon="information-circle" color="info">
                        {{ $selectedSignal['description'] }}
                    </x-alert>
                @endif

                <div class="flex gap-2">
                    <x-button wire:click="acknowledgeSelectedSignal" color="blue">{{ __('Acknowledge') }}</x-button>
                    <x-button wire:click="mitigateSelectedSignal" color="green">{{ __('Mark Mitigated') }}</x-button>
                </div>
            </div>
        @endif
    </x-slide>

    {{-- Recommendation Slide --}}
    <x-slide wire:model="slideRecommendation" title="{{ __('Recommendation') }}" size="lg">
        @if($selectedRecommendation)
            <div class="space-y-4">
                <div class="text-sm text-slate-400">
                    <div><strong class="text-slate-200">{{ __('Department') }}:</strong> {{ $selectedRecommendation['department'] }}</div>
                    <div><strong class="text-slate-200">{{ __('Priority') }}:</strong> {{ ucfirst($selectedRecommendation['priority']) }}</div>
                    <div><strong class="text-slate-200">{{ __('Generated') }}:</strong> {{ $selectedRecommendation['generated_at'] ?? '—' }}</div>
                </div>

                <div class="text-slate-200 font-semibold">{{ $selectedRecommendation['title'] }}</div>
                <div class="text-slate-300 whitespace-pre-line">{{ $selectedRecommendation['text'] }}</div>

                <div class="flex gap-2">
                    <x-button wire:click="acknowledgeSelectedRecommendation" color="blue">{{ __('Acknowledge') }}</x-button>
                </div>
            </div>
        @endif
    </x-slide>
</div>
