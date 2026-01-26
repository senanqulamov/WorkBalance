<?php

namespace App\Livewire\HumanOps\Prevention;

use App\Models\BurnoutRiskSignal;
use App\Models\FinancialStressSignal;
use App\Models\Recommendation;
use App\Models\RelationshipHealthSignal;
use App\Services\HumanOps\HumanOpsDataService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public bool $slideSignal = false;
    public bool $slideRecommendation = false;

    public ?array $selectedSignal = null;
    public ?array $selectedRecommendation = null;

    public array $signalHeaders = [
        ['index' => 'type', 'label' => 'Type'],
        ['index' => 'severity', 'label' => 'Severity'],
        ['index' => 'department', 'label' => 'Department'],
        ['index' => 'detected_at', 'label' => 'Detected'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'action', 'label' => 'Action', 'sortable' => false],
    ];

    public array $recommendationHeaders = [
        ['index' => 'priority', 'label' => 'Priority'],
        ['index' => 'title', 'label' => 'Title'],
        ['index' => 'department', 'label' => 'Department'],
        ['index' => 'generated_at', 'label' => 'Generated'],
        ['index' => 'action', 'label' => 'Action', 'sortable' => false],
    ];

    public function mount(HumanOpsDataService $data): void
    {
        $data->logView('prevention');
    }

    #[Computed]
    public function signals(): array
    {
        $signals = collect();

        $burnout = BurnoutRiskSignal::with('department')->orderByDesc('detected_at')->limit(20)->get();
        foreach ($burnout as $s) {
            $signals->push([
                'id' => $s->id,
                'type_key' => 'burnout',
                'type' => 'Burnout',
                'severity' => $s->risk_level,
                'department' => $s->department?->name ?? 'Organization',
                'detected_at' => $s->detected_at?->diffForHumans() ?? '—',
                'status' => $s->mitigated_at ? 'Mitigated' : ($s->acknowledged_at ? 'Acknowledged' : 'New'),
            ]);
        }

        $financial = FinancialStressSignal::with('department')->orderByDesc('detected_at')->limit(20)->get();
        foreach ($financial as $s) {
            $signals->push([
                'id' => $s->id,
                'type_key' => 'financial',
                'type' => 'Financial',
                'severity' => $s->stress_level,
                'department' => $s->department?->name ?? 'Organization',
                'detected_at' => $s->detected_at?->diffForHumans() ?? '—',
                'status' => $s->mitigated_at ? 'Mitigated' : ($s->acknowledged_at ? 'Acknowledged' : 'New'),
            ]);
        }

        $relationship = RelationshipHealthSignal::with('department')->orderByDesc('detected_at')->limit(20)->get();
        foreach ($relationship as $s) {
            $signals->push([
                'id' => $s->id,
                'type_key' => 'relationship',
                'type' => 'Relationships',
                'severity' => $s->strain_level,
                'department' => $s->department?->name ?? 'Organization',
                'detected_at' => $s->detected_at?->diffForHumans() ?? '—',
                'status' => $s->mitigated_at ? 'Mitigated' : ($s->acknowledged_at ? 'Acknowledged' : 'New'),
            ]);
        }

        return $signals->sortByDesc('detected_at')->values()->toArray();
    }

    #[Computed]
    public function recommendations(): array
    {
        return Recommendation::query()
            ->with('department')
            ->where('generated_at', '>=', now()->subDays(45))
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderByDesc('generated_at')
            ->limit(15)
            ->get()
            ->map(fn(Recommendation $r) => [
                'id' => $r->id,
                'priority' => ucfirst($r->priority),
                'title' => $r->title,
                'department' => $r->department?->name ?? 'Organization',
                'generated_at' => $r->generated_at?->diffForHumans() ?? '—',
                'acknowledged_at' => $r->acknowledged_at,
                'text' => $r->text,
            ])
            ->toArray();
    }

    public function openSignal(string $typeKey, int $id): void
    {
        $model = match ($typeKey) {
            'burnout' => BurnoutRiskSignal::class,
            'financial' => FinancialStressSignal::class,
            'relationship' => RelationshipHealthSignal::class,
            default => null,
        };

        if (!$model) {
            return;
        }

        $s = $model::with('department')->find($id);
        if (!$s) {
            return;
        }

        $this->selectedSignal = [
            'id' => $s->id,
            'type_key' => $typeKey,
            'department' => $s->department?->name ?? 'Organization',
            'description' => $s->description ?? null,
            'detected_at' => $s->detected_at?->toDayDateTimeString() ?? null,
            'acknowledged_at' => $s->acknowledged_at,
            'mitigated_at' => $s->mitigated_at,
            'severity' => $typeKey === 'burnout' ? $s->risk_level : ($typeKey === 'financial' ? $s->stress_level : $s->strain_level),
        ];

        $this->slideSignal = true;
    }

    public function acknowledgeSelectedSignal(): void
    {
        if (!$this->selectedSignal) {
            return;
        }

        $typeKey = $this->selectedSignal['type_key'];
        $id = $this->selectedSignal['id'];

        $model = match ($typeKey) {
            'burnout' => BurnoutRiskSignal::class,
            'financial' => FinancialStressSignal::class,
            'relationship' => RelationshipHealthSignal::class,
            default => null,
        };

        if ($model) {
            $row = $model::find($id);
            if ($row && !$row->acknowledged_at) {
                $row->update(['acknowledged_at' => now()]);
            }
        }

        $this->slideSignal = false;
        $this->selectedSignal = null;
    }

    public function mitigateSelectedSignal(): void
    {
        if (!$this->selectedSignal) {
            return;
        }

        $typeKey = $this->selectedSignal['type_key'];
        $id = $this->selectedSignal['id'];

        $model = match ($typeKey) {
            'burnout' => BurnoutRiskSignal::class,
            'financial' => FinancialStressSignal::class,
            'relationship' => RelationshipHealthSignal::class,
            default => null,
        };

        if ($model) {
            $row = $model::find($id);
            if ($row && !$row->mitigated_at) {
                $row->update([
                    'mitigated_at' => now(),
                    'acknowledged_at' => $row->acknowledged_at ?? now(),
                ]);
            }
        }

        $this->slideSignal = false;
        $this->selectedSignal = null;
    }

    public function openRecommendation(int $id): void
    {
        $r = Recommendation::with('department')->find($id);
        if (!$r) {
            return;
        }

        $this->selectedRecommendation = [
            'id' => $r->id,
            'priority' => $r->priority,
            'title' => $r->title,
            'text' => $r->text,
            'department' => $r->department?->name ?? 'Organization',
            'generated_at' => $r->generated_at?->toDayDateTimeString(),
            'acknowledged_at' => $r->acknowledged_at,
        ];

        $this->slideRecommendation = true;
    }

    public function acknowledgeSelectedRecommendation(): void
    {
        if (!$this->selectedRecommendation) {
            return;
        }

        $r = Recommendation::find($this->selectedRecommendation['id']);
        if ($r && !$r->acknowledged_at) {
            $r->update(['acknowledged_at' => now()]);
        }

        $this->slideRecommendation = false;
        $this->selectedRecommendation = null;
    }

    public function render(): View
    {
        return view('livewire.humanops.prevention.index');
    }
}
