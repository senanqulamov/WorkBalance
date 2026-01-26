<?php

namespace App\Livewire\WorkBalance\Trends;

use App\Models\DailyCheckIn;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public string $timeRange = '30days';
    public $trendData = [];
    public $patterns = [];
    public $statistics = [];

    public function mount(): void
    {
        $this->loadTrends();
    }

    public function setTimeRange(string $range): void
    {
        $this->timeRange = $range;
        $this->loadTrends();
    }

    protected function loadTrends()
    {
        $daysBack = match($this->timeRange) {
            '7days' => 7,
            '14days' => 14,
            '30days' => 30,
            '90days' => 90,
            default => 30,
        };

        $checkIns = DailyCheckIn::where('user_id', auth()->id())
            ->where('check_in_date', '>=', now()->subDays($daysBack))
            ->orderBy('check_in_date')
            ->get();

        if ($checkIns->isEmpty()) {
            $this->trendData = [];
            $this->patterns = [];
            $this->statistics = [];
            return;
        }

        $this->trendData = $checkIns->map(fn($checkIn) => [
            'date' => $checkIn->check_in_date->format('M d'),
            'stress' => $checkIn->stress_value,
            'energy' => $checkIn->energy_value,
            'mood' => $checkIn->mood_state,
            'wellness' => round(($checkIn->energy_value + (6 - $checkIn->stress_value)) / 2 * 2, 1),
        ])->toArray();

        $this->statistics = [
            'avg_stress' => round($checkIns->avg('stress_value'), 1),
            'avg_energy' => round($checkIns->avg('energy_value'), 1),
            'check_in_count' => $checkIns->count(),
            'best_day' => $this->findBestDay($checkIns),
            'toughest_day' => $this->findToughestDay($checkIns),
        ];

        $this->detectPatterns($checkIns, $daysBack);
    }

    protected function findBestDay($checkIns)
    {
        $best = $checkIns->sortByDesc(fn($c) =>
            ($c->energy_value + (6 - $c->stress_value))
        )->first();

        return $best ? [
            'date' => $best->check_in_date->format('M d'),
            'wellness' => round(($best->energy_value + (6 - $best->stress_value)) / 2 * 2, 1),
        ] : null;
    }

    protected function findToughestDay($checkIns)
    {
        $toughest = $checkIns->sortBy(fn($c) =>
            ($c->energy_value + (6 - $c->stress_value))
        )->first();

        return $toughest ? [
            'date' => $toughest->check_in_date->format('M d'),
            'wellness' => round(($toughest->energy_value + (6 - $toughest->stress_value)) / 2 * 2, 1),
        ] : null;
    }

    protected function detectPatterns($checkIns, $daysBack)
    {
        $patterns = [];

        $byDay = $checkIns->groupBy(fn($c) => $c->check_in_date->format('l'));
        $dayAverages = $byDay->map(fn($group) => round($group->avg('energy_level'), 1));
        $lowestEnergyDay = $dayAverages->sort()->keys()->first();

        if ($lowestEnergyDay) {
            $patterns[] = [
                'title' => 'Weekly Energy Pattern',
                'description' => "Your energy tends to be lowest on {$lowestEnergyDay}s.",
            ];
        }

        $recentStress = $checkIns->slice(-7)->avg('stress_level');
        $earlierStress = $checkIns->slice(0, min(7, $checkIns->count() - 7))->avg('stress_level');

        if ($recentStress > $earlierStress + 1) {
            $patterns[] = [
                'title' => 'Stress Rising',
                'description' => 'Your stress has been climbing recently.',
            ];
        }

        if (($checkIns->count() / $daysBack) * 100 > 70) {
            $patterns[] = [
                'title' => 'Consistent Check-Ins',
                'description' => "You've been checking in regularly—this helps spot patterns early.",
            ];
        }

        $this->patterns = $patterns;
    }

    public function render(): View
    {
        return view('livewire.workbalance.trends.index');
    }
}
