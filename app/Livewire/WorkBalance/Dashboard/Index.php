<?php

namespace App\Livewire\WorkBalance\Dashboard;

use App\Models\DailyCheckIn;
use App\Models\WellBeingToolUsage;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public int $stressLevel = 3;
    public int $energyLevel = 3;
    public string $moodState = 'okay';

    public bool $showSuccess = false;
    public bool $alreadyCheckedInToday = false;

    public $todayCheckIn = null;
    public array $recentTrends = [];
    public array $toolUsageStats = [];

    public function mount(): void
    {
        $this->loadTodayCheckIn();
        $this->loadRecentTrends();
        $this->loadToolUsage();
    }

    protected function loadTodayCheckIn(): void
    {
        $this->todayCheckIn = DailyCheckIn::where('user_id', auth()->id())
            ->where('check_in_date', today())
            ->first();

        if ($this->todayCheckIn) {
            $this->alreadyCheckedInToday = true;
            $this->stressLevel = (int)($this->todayCheckIn->stress_value ?? 3);
            $this->energyLevel = (int)($this->todayCheckIn->energy_value ?? 3);
            $this->moodState = (string)($this->todayCheckIn->mood_state ?? 'okay');
        }
    }

    protected function loadRecentTrends(): void
    {
        $checkIns = DailyCheckIn::where('user_id', auth()->id())
            ->where('check_in_date', '>=', now()->subDays(7))
            ->orderBy('check_in_date')
            ->get();

        $this->recentTrends = [
            'avg_stress' => round((float)$checkIns->avg('stress_value'), 1),
            'avg_energy' => round((float)$checkIns->avg('energy_value'), 1),
            'check_in_count' => $checkIns->count(),
        ];
    }

    protected function loadToolUsage(): void
    {
        $usage = WellBeingToolUsage::query()
            ->with('tool')
            ->where('user_id', auth()->id())
            ->where('used_at', '>=', now()->subDays(30))
            ->get();

        $mostUsedTool = $usage
            ->groupBy('tool_id')
            ->sortByDesc(fn($group) => $group->count())
            ->first();

        $this->toolUsageStats = [
            'total_sessions' => $usage->count(),
            'completed_sessions' => $usage->where('completed', true)->count(),
            'most_used_tool' => $mostUsedTool?->first()?->tool?->title ?? 'None',
        ];
    }

    public function submit(): void
    {
        $this->validate([
            'stressLevel' => 'required|integer|min:1|max:5',
            'energyLevel' => 'required|integer|min:1|max:5',
            'moodState' => 'required|string|max:50',
        ]);

        $stressEnum = $this->stressLevel <= 2 ? 'low' : ($this->stressLevel <= 4 ? 'medium' : 'high');
        $energyEnum = $this->energyLevel <= 2 ? 'low' : ($this->energyLevel <= 4 ? 'medium' : 'high');

        DailyCheckIn::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'check_in_date' => today(),
            ],
            [
                'stress_value' => $this->stressLevel,
                'energy_value' => $this->energyLevel,
                'stress_level' => $stressEnum,
                'energy_level' => $energyEnum,
                'mood_state' => $this->moodState,
            ]
        );

        $this->showSuccess = true;
        $this->alreadyCheckedInToday = true;

        $this->loadRecentTrends();

        $this->dispatch('checkin-submitted');
    }

    public function render(): View
    {
        return view('livewire.workbalance.dashboard.index');
    }
}
