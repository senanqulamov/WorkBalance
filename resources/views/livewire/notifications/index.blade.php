<div>
    <x-card>
        <x-slot name="header">
            <div class="flex items-center justify-between gap-5">
                <h3 class="text-lg font-semibold text-slate-200">{{ __('Notifications') }}</h3>
                <div class="flex items-center gap-2">
                    <select wire:model.live="type" class="px-3 py-2 rounded w-full bg-slate-200 border border-slate-200 text-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach($this->types as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                    <x-button wire:click="markAllRead" sm class="w-full">{{ __('Mark all as read') }}</x-button>
                </div>
            </div>
        </x-slot>

        <div class="divide-y divide-slate-800/50">
            @forelse($this->rows as $n)
                <div class="flex items-start justify-between p-4 hover:bg-slate-800/30 rounded-xl">
                    <div class="flex items-start gap-3">
                        <div>
                            <x-badge :color="$n->read_at ? 'slate' : 'primary'" light sm>
                                {{ $n->read_at ? __('Read') : __('Unread') }}
                            </x-badge>
                        </div>
                        <div class="space-y-1">
                            <div class="text-sm font-semibold">{{ class_basename($n->type) }}</div>
                            <div class="text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</div>
                            <pre class="text-xs text-slate-300 bg-slate-900/50 p-2 rounded">{{ json_encode($n->data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @if(!$n->read_at)
                        <x-button wire:click="markRead('{{ $n->id }}')" sm outline>{{ __('Mark as read') }}</x-button>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">{{ __('No notifications') }}</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $this->rows->links() }}</div>
    </x-card>
</div>
