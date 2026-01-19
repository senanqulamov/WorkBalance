@props(['n'])

@php
$type = class_basename($n->type);
if (is_array($n->data)) {
    $data = $n->data;
} else {
    $data = json_decode($n->data, true) ?: [];
}
@endphp

<div class="min-w-0">
    <div class="flex items-center gap-3">
        <div class="text-sm font-semibold text-slate-200">@switch($type)
            @case('RequestStatusUpdated')
                {{ __('RFQ status updated') }}
                @break
            @case('SlaReminder')
                {{ __('SLA reminder') }}
                @break
            @case('SupplierInvitation')
                {{ __('Supplier invitation') }}
                @break
            @case('QuoteReceived')
                {{ __('Quote received') }}
                @break
            @default
                {{ class_basename($n->type) }}
        @endswitch</div>
        <div class="text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</div>
    </div>

    <div class="mt-1 text-sm text-slate-300">
        @switch($type)
            @case('RequestStatusUpdated')
                <div>{{ __('RFQ #:id — :title', ['id' => $data['request_id'] ?? 'N/A', 'title' => $data['request_title'] ?? '']) }}</div>
                <div class="text-xs text-slate-400">{{ __('Status: :old → :new', ['old' => $data['old_status_label'] ?? $data['old_status'] ?? '', 'new' => $data['new_status_label'] ?? $data['new_status'] ?? '']) }}</div>
                <div class="mt-2"><a href="{{ url('/rfq/' . ($data['request_id'] ?? '')) }}" class="text-primary-500 hover:underline">{{ __('View RFQ') }}</a></div>
                @break

            @case('SlaReminder')
                <div>{{ __('RFQ #:id is due in :days day(s)', ['id' => $data['request_id'] ?? 'N/A', 'days' => $data['days_remaining'] ?? $data['days'] ?? '?']) }}</div>
                <div class="text-xs text-slate-400">{{ __('Priority: :p', ['p' => ucfirst($data['priority'] ?? 'medium')]) }}</div>
                <div class="mt-2"><a href="{{ url('/rfq/' . ($data['request_id'] ?? '')) }}" class="text-primary-500 hover:underline">{{ __('Open RFQ to review') }}</a></div>
                @break

            @case('SupplierInvitation')
                <div>{{ __('You have been invited to RFQ #:id', ['id' => $data['request_id'] ?? 'N/A']) }}</div>
                <div class="text-xs text-slate-400">{{ $data['message'] ?? '' }}</div>
                <div class="mt-2"><a href="{{ url('/invitations') }}" class="text-primary-500 hover:underline">{{ __('View invitations') }}</a></div>
                @break

            @case('QuoteReceived')
                <div>{{ __('New quote received for RFQ #:id', ['id' => $data['request_id'] ?? 'N/A']) }}</div>
                <div class="text-xs text-slate-400">{{ __('Supplier: :s', ['s' => $data['supplier_name'] ?? '']) }}</div>
                <div class="mt-2"><a href="{{ url('/rfq/' . ($data['request_id'] ?? '')) }}" class="text-primary-500 hover:underline">{{ __('View quote') }}</a></div>
                @break

            @default
                <div class="text-xs text-slate-400">{{ \Illuminate\Support\Str::limit(strip_tags(json_encode($data, JSON_PRETTY_PRINT)), 200) }}</div>
        @endswitch
    </div>
</div>
