<div>
    <x-card>
        <x-heading-title title="{{ __('Logs') }}" text="" icon="clipboard-document-list" padding="p-5" hover="-"/>

        {{-- Filter Section --}}
        <div class="mb-4 mt-4 flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <x-select.styled
                    label="Filter by Type"
                    wire:model.live="typeFilter"
                    :options="collect($this->logTypes)->map(fn($type) => ['label' => ucfirst($type), 'value' => $type])->toArray()"
                    select="label:label|value:value"
                />
            </div>
            @if($typeFilter)
                <x-button color="red" text="Clear Filter" wire:click="clearTypeFilter" />
            @endif
        </div>

        {{-- Table --}}
        <x-table :$headers :$sort :rows="$this->rows" paginate :paginator="null" filter loading :quantity="[5, 10, 20, 50, 'all']">
            @interact('column_type', $row)
            @php
                $typeConfig = match($row->type) {
                    'create' => ['color' => 'green', 'icon' => 'plus'],
                    'update' => ['color' => 'blue', 'icon' => 'pencil'],
                    'delete' => ['color' => 'red', 'icon' => 'trash'],
                    'page_view' => ['color' => 'purple', 'icon' => 'eye'],
                    'auth' => ['color' => 'yellow', 'icon' => 'lock-closed'],
                    'error' => ['color' => 'red', 'icon' => 'exclamation-triangle'],
                    'export' => ['color' => 'indigo', 'icon' => 'arrow-down-tray'],
                    'import' => ['color' => 'indigo', 'icon' => 'arrow-up-tray'],
                    'bulk' => ['color' => 'orange', 'icon' => 'square-3-stack-3d'],
                    'system' => ['color' => 'slate', 'icon' => 'cog'],
                    'security' => ['color' => 'pink', 'icon' => 'shield-check'],
                    'config' => ['color' => 'cyan', 'icon' => 'wrench'],
                    default => ['color' => 'gray', 'icon' => 'information-circle']
                };
            @endphp
            <div class="flex items-center gap-2">
                <div class="rounded-full bg-{{ $typeConfig['color'] }}-100 dark:bg-{{ $typeConfig['color'] }}-900 p-1.5">
                    <x-icon name="{{ $typeConfig['icon'] }}" class="h-4 w-4 text-{{ $typeConfig['color'] }}-600 dark:text-{{ $typeConfig['color'] }}-400" />
                </div>
                <x-badge :text="ucfirst($row->type)" :color="$typeConfig['color']" sm />
            </div>
            @endinteract

            @interact('column_action', $row)
            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $row->action ?? '-' }}</span>
            @endinteract

            @interact('column_message', $row)
            <div class="max-w-xs truncate" title="{{ $row->message }}">
                {{ Str::limit($row->message, 50) }}
            </div>
            @endinteract

            @interact('column_user_id', $row)
            @if($row->user)
                <span class="text-sm">{{ $row->user->name }}</span>
            @else
                <span class="text-gray-400 text-sm">-</span>
            @endif
            @endinteract

            @interact('column_ip_address', $row)
            <span class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ $row->ip_address ?? '-' }}</span>
            @endinteract

            @interact('column_created_at', $row)
            <span class="text-xs" title="{{ $row->created_at->format('Y-m-d H:i:s') }}">
                {{ $row->created_at->diffForHumans() }}
            </span>
            @endinteract

            @interact('column_action_column', $row)
            <div class="flex gap-1">
                <x-button.circle
                    icon="eye"
                    color="blue"
                    wire:click="$dispatchTo('logs.log-view', 'load::log', { 'log' : '{{ $row->id }}'})"
                />
            </div>
            @endinteract
        </x-table>
    </x-card>

    <livewire:logs.log-view />
</div>
