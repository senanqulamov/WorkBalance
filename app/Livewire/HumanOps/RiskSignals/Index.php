<?php

namespace App\Livewire\HumanOps\RiskSignals;

use App\Models\BurnoutRiskSignal;
use App\Models\FinancialStressSignal;
use App\Models\RelationshipHealthSignal;
use App\Services\HumanOps\HumanOpsDataService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public string $filterSeverity = 'all';
    public string $filterType = 'all';
    public bool $showMitigated = false;

    public array $headers = [
        ['index' => 'type_label', 'label' => 'Type'],
        ['index' => 'severity', 'label' => 'Severity'],
        ['index' => 'department', 'label' => 'Department'],
        ['index' => 'detected_at', 'label' => 'Detected'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'action', 'label' => 'Action', 'sortable' => false],
    ];

    public function mount(HumanOpsDataService $data): void
    {
        $data->logView('risk-signals');
    }

    public function acknowledgeSignal(string $type, int $signalId): void
    {
        $signal = $this->findSignal($type, $signalId);

        if ($signal && $signal->acknowledged_at === null) {
            $signal->update(['acknowledged_at' => now()]);
            session()->flash('message', 'Signal acknowledged.');
        }
    }

    public function mitigateSignal(string $type, int $signalId): void
    {
        $signal = $this->findSignal($type, $signalId);

        if ($signal && $signal->mitigated_at === null) {
            $signal->update([
                'mitigated_at' => now(),
                'acknowledged_at' => $signal->acknowledged_at ?? now(),
            ]);

            session()->flash('message', 'Signal marked as mitigated.');
        }
    }

    protected function findSignal(string $type, int $id)
    {
        return match ($type) {
            'burnout' => BurnoutRiskSignal::find($id),
            'financial' => FinancialStressSignal::find($id),
            'relationship' => RelationshipHealthSignal::find($id),
            default => null,
        };
    }

    #[Computed]
    public function signalTypes(): array
    {
        return [
            ['label' => 'All Types', 'value' => 'all'],
            ['label' => 'Burnout Risk', 'value' => 'burnout'],
            ['label' => 'Financial Stress', 'value' => 'financial'],
            ['label' => 'Relationship Health', 'value' => 'relationship'],
        ];
    }

    #[Computed]
    public function severityOptions(): array
    {
        return [
            ['label' => 'All Severities', 'value' => 'all'],
            ['label' => 'High', 'value' => 'high'],
            ['label' => 'Medium', 'value' => 'medium'],
            ['label' => 'Low', 'value' => 'low'],
        ];
    }

    #[Computed]
    public function rows(): array
    {
        $signals = collect();

        if ($this->filterType === 'all' || $this->filterType === 'burnout') {
            $signals = $signals->merge(
                BurnoutRiskSignal::query()
                    ->with('department')
                    ->when($this->filterSeverity !== 'all', fn($q) => $q->where('risk_level', $this->filterSeverity))
                    ->when(!$this->showMitigated, fn($q) => $q->whereNull('mitigated_at'))
                    ->orderByDesc('detected_at')
                    ->get()
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'type' => 'burnout',
                        'type_label' => 'Burnout Risk',
                        'severity' => $s->risk_level,
                        'department' => $s->department?->name ?? 'Organization',
                        'detected_at' => $s->detected_at?->diffForHumans() ?? '—',
                        'status' => $s->mitigated_at ? 'Mitigated' : ($s->acknowledged_at ? 'Acknowledged' : 'New'),
                    ])
            );
        }

        if ($this->filterType === 'all' || $this->filterType === 'financial') {
            $signals = $signals->merge(
                FinancialStressSignal::query()
                    ->with('department')
                    ->when($this->filterSeverity !== 'all', fn($q) => $q->where('stress_level', $this->filterSeverity))
                    ->when(!$this->showMitigated, fn($q) => $q->whereNull('mitigated_at'))
                    ->orderByDesc('detected_at')
                    ->get()
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'type' => 'financial',
                        'type_label' => 'Financial Stress',
                        'severity' => $s->stress_level,
                        'department' => $s->department?->name ?? 'Organization',
                        'detected_at' => $s->detected_at?->diffForHumans() ?? '—',
                        'status' => $s->mitigated_at ? 'Mitigated' : ($s->acknowledged_at ? 'Acknowledged' : 'New'),
                    ])
            );
        }

        if ($this->filterType === 'all' || $this->filterType === 'relationship') {
            $signals = $signals->merge(
                RelationshipHealthSignal::query()
                    ->with('department')
                    ->when($this->filterSeverity !== 'all', fn($q) => $q->where('strain_level', $this->filterSeverity))
                    ->when(!$this->showMitigated, fn($q) => $q->whereNull('mitigated_at'))
                    ->orderByDesc('detected_at')
                    ->get()
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'type' => 'relationship',
                        'type_label' => 'Relationship Health',
                        'severity' => $s->strain_level,
                        'department' => $s->department?->name ?? 'Organization',
                        'detected_at' => $s->detected_at?->diffForHumans() ?? '—',
                        'status' => $s->mitigated_at ? 'Mitigated' : ($s->acknowledged_at ? 'Acknowledged' : 'New'),
                    ])
            );
        }

        return $signals->sortByDesc('detected_at')->values()->toArray();
    }

    public function render(): View
    {
        return view('livewire.humanops.risk-signals.index');
    }
}
