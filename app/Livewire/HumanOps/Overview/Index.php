<?php

namespace App\Livewire\HumanOps\Overview;

use App\Models\AggregatedWellbeingSignal;
use App\Models\BurnoutRiskSignal;
use App\Models\FinancialStressSignal;
use App\Models\Recommendation;
use App\Services\HumanOps\HumanOpsDataService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public array $overviewData = [];
    public array $riskSignals = [];
    public array $recommendations = [];
    public array $participationStats = [];

    public function mount(HumanOpsDataService $data): void
    {
        $data->logView('overview');

        $this->loadOverviewData();
        $this->loadRiskSignals();
        $this->loadRecommendations();
        $this->loadParticipationStats();
    }

    protected function loadOverviewData(): void
    {
        $latest = AggregatedWellbeingSignal::whereNull('department_id')->latest('period_end')->first();

        if (!$latest) {
            $this->overviewData = [
                'status' => 'insufficient_data',
                'message' => 'Collecting baseline data...',
            ];
            return;
        }

        $this->overviewData = [
            'avg_stress' => $latest->avg_stress ?? 0,
            'avg_energy' => $latest->avg_energy ?? 0,
            'mood_index' => $latest->mood_index ?? 0,
            'participant_count' => $latest->participant_count,
            'confidence' => $latest->data_confidence,
            'period_end' => $latest->period_end->format('M d, Y'),
            'wellness_score' => $this->calculateWellness($latest),
        ];
    }

    protected function calculateWellness(AggregatedWellbeingSignal $signal): float
    {
        $stress = (float)($signal->avg_stress ?? 3);
        $energy = (float)($signal->avg_energy ?? 3);
        return round((($energy + (6 - $stress)) / 2) * 2, 1);
    }

    protected function loadRiskSignals(): void
    {
        $signals = collect();

        $burnout = BurnoutRiskSignal::whereNull('mitigated_at')
            ->where('detected_at', '>=', now()->subDays(30))
            ->orderByDesc('detected_at')
            ->limit(3)
            ->get();

        foreach ($burnout as $signal) {
            $signals->push([
                'type' => 'Burnout Risk',
                'level' => $signal->risk_level,
                'department' => $signal->department?->name ?? 'Organization',
                'detected' => $signal->detected_at->diffForHumans(),
            ]);
        }

        $financial = FinancialStressSignal::whereNull('mitigated_at')
            ->where('detected_at', '>=', now()->subDays(30))
            ->orderByDesc('detected_at')
            ->limit(2)
            ->get();

        foreach ($financial as $signal) {
            $signals->push([
                'type' => 'Financial Stress',
                'level' => $signal->stress_level,
                'department' => $signal->department?->name ?? 'Organization',
                'detected' => $signal->detected_at->diffForHumans(),
            ]);
        }

        $this->riskSignals = $signals->toArray();
    }

    protected function loadRecommendations(): void
    {
        $this->recommendations = Recommendation::whereNull('acknowledged_at')
            ->where('generated_at', '>=', now()->subDays(30))
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->limit(3)
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'priority' => $r->priority,
                'department' => $r->department?->name ?? 'Organization',
            ])
            ->toArray();
    }

    /**
     * Aggregated-only participation snapshot.
     */
    protected function loadParticipationStats(): void
    {
        $latest = AggregatedWellbeingSignal::latest('period_end')->first();

        $this->participationStats = [
            'participants' => (int)($latest?->participant_count ?? 0),
            'confidence' => (float)($latest?->data_confidence ?? 0),
            'period' => $latest?->period_end?->format('M d, Y'),
        ];
    }

    public function render(): View
    {
        return view('livewire.humanops.overview.index');
    }
}
