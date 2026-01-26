<?php

namespace App\Livewire\HumanOps\Trends;

use App\Models\AggregatedWellbeingSignal;
use App\Services\HumanOps\HumanOpsDataService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public string $timeRange = '30days';
    public array $trendData = [];
    public array $insights = [];

    public function mount(HumanOpsDataService $data): void
    {
        $data->logView('trends');
        $this->loadTrends();
    }

    public function updatedTimeRange(): void
    {
        $this->loadTrends();
    }

    protected function loadTrends(): void
    {
        $daysBack = match ($this->timeRange) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 30,
        };

        $signals = AggregatedWellbeingSignal::query()
            ->where('period_start', '>=', now()->subDays($daysBack)->startOfDay())
            ->where('data_confidence', '>', 0)
            ->orderBy('period_start')
            ->get();

        $this->trendData = $signals->map(fn(AggregatedWellbeingSignal $signal) => [
            'date' => $signal->period_start->format('M d'),
            'date_full' => $signal->period_start->format('Y-m-d'),
            // Convert 1–5 values to 0–10-ish display values
            'stress_score' => (float)($signal->avg_stress ?? 0) * 2,
            'energy_score' => (float)($signal->avg_energy ?? 0) * 2,
            'mood_score' => (float)($signal->mood_index ?? 0) * 2,
            'participants' => (int)$signal->participant_count,
            'confidence' => (float)$signal->data_confidence,
            'period' => $signal->period,
        ])->toArray();

        $this->generateInsights($signals);
    }

    /**
     * @param \Illuminate\Support\Collection<int, AggregatedWellbeingSignal> $signals
     */
    protected function generateInsights($signals): void
    {
        $insights = [];

        if ($signals->count() < 2) {
            $this->insights = [[
                'type' => 'neutral',
                'title' => 'Collecting Baseline Data',
                'description' => 'Insufficient historical data for trend analysis yet. Continue monitoring as more aggregated data becomes available.',
            ]];
            return;
        }

        $earliest = $signals->first();
        $latest = $signals->last();

        $stressChange = (float)($latest->avg_stress ?? 0) - (float)($earliest->avg_stress ?? 0);
        $energyChange = (float)($latest->avg_energy ?? 0) - (float)($earliest->avg_energy ?? 0);
        $participationChange = (int)($latest->participant_count ?? 0) - (int)($earliest->participant_count ?? 0);

        if ($stressChange > 0.5) {
            $insights[] = [
                'type' => 'concern',
                'title' => 'Stress is trending up',
                'description' => sprintf('Average stress increased by %.1f (1–5 scale). Consider reviewing workload and support resources.', $stressChange),
            ];
        } elseif ($stressChange < -0.5) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Stress is trending down',
                'description' => sprintf('Average stress decreased by %.1f (1–5 scale). Keep reinforcing what is working.', abs($stressChange)),
            ];
        }

        if ($energyChange < -0.5) {
            $insights[] = [
                'type' => 'concern',
                'title' => 'Energy is trending down',
                'description' => 'Lower energy over time can indicate overload and recovery issues. Consider recovery time and sustainable pacing.',
            ];
        } elseif ($energyChange > 0.5) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Energy is trending up',
                'description' => 'Higher energy over time suggests conditions are improving. Protect this momentum with consistent practices.',
            ];
        }

        if (abs($participationChange) >= 3) {
            $insights[] = [
                'type' => $participationChange > 0 ? 'positive' : 'neutral',
                'title' => $participationChange > 0 ? 'Participation increasing' : 'Participation decreasing',
                'description' => $participationChange > 0
                    ? 'More employees are participating, which usually signals trust in privacy and usefulness.'
                    : 'Fewer employees are participating recently. Consider reinforcing the privacy promise and value of the system.',
            ];
        }

        if (empty($insights)) {
            $insights[] = [
                'type' => 'neutral',
                'title' => 'Stable trends',
                'description' => 'No significant changes detected across this time range.',
            ];
        }

        $this->insights = $insights;
    }

    public function render(): View
    {
        return view('livewire.humanops.trends.index');
    }
}
