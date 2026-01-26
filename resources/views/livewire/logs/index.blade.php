<div>
    <x-card>
        <x-heading-title title="{{ __('Activity Signals') }}" text="Anonymized activity patterns for system insights" icon="signal" padding="p-5" hover="-"/>

        {{-- Filter Section --}}
        <div class="mb-4 mt-4 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <x-select.styled
                    label="Filter by Action Type"
                    wire:model.live="typeFilter"
                    :options="collect($this->actionTypes)->map(fn($type) => ['label' => ucfirst($type), 'value' => $type])->toArray()"
                    select="label:label|value:value"
                />
            </div>
            @if($typeFilter)
                <x-button color="red" text="Clear Filter" wire:click="clearTypeFilter" />
            @endif
        </div>

        {{-- Table --}}
        <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 50, 'all']">
            @interact('column_action_type', $row)
            @php
                $typeConfig = match($row->action_type) {
                    'check_in_completed' => ['color' => 'green', 'icon' => 'check-circle'],
                    'path_started' => ['color' => 'blue', 'icon' => 'play'],
                    'path_completed' => ['color' => 'purple', 'icon' => 'flag'],
                    'reflection_created' => ['color' => 'indigo', 'icon' => 'pencil'],
                    'session_started' => ['color' => 'cyan', 'icon' => 'spark'],
                    'session_completed' => ['color' => 'green', 'icon' => 'check-badge'],
                    'stress_trend_changed' => ['color' => 'yellow', 'icon' => 'chart-line'],
                    'burnout_threshold_crossed' => ['color' => 'orange', 'icon' => 'exclamation-triangle'],
                    'team_metric_aggregated' => ['color' => 'slate', 'icon' => 'chart-bar'],
                    default => ['color' => 'gray', 'icon' => 'information-circle']
                };
            @endphp
            <div class="flex items-center gap-2">
                <div class="rounded-full bg-{{ $typeConfig['color'] }}-100 dark:bg-{{ $typeConfig['color'] }}-900 p-1.5">
                    <x-icon name="{{ $typeConfig['icon'] }}" class="h-4 w-4 text-{{ $typeConfig['color'] }}-600 dark:text-{{ $typeConfig['color'] }}-400" />
                </div>
                <x-badge :text="Str::replace('_', ' ', Str::title($row->action_type))" :color="$typeConfig['color']" sm />
            </div>
            @endinteract

            @interact('column_description', $row)
            <div class="max-w-xs truncate" title="{{ $row->description }}">
                {{ Str::limit($row->description, 50) }}
            </div>
            @endinteract

            @interact('column_team_id', $row)
            @if($row->team)
                <span class="text-sm">{{ $row->team->name }}</span>
            @else
                <span class="text-gray-400 text-sm">Anonymous</span>
            @endif
            @endinteract

            @interact('column_occurred_at', $row)
            <span class="text-xs" title="{{ $row->occurred_at->format('Y-m-d H:i:s') }}">
                {{ $row->occurred_at->diffForHumans() }}
            </span>
            @endinteract

            @interact('column_action_column', $row)
            <div class="flex gap-1">
                <x-button.circle
                    icon="eye"
                    color="blue"
                    wire:click="$dispatchTo('logs.log-view', 'load::activity-signal', { 'signalId' : '{{ $row->id }}'})"
                />
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:logs.log-view />
</div>
