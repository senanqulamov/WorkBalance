<?php

namespace App\Livewire\HumanOps\WellBeing;

use App\Models\AggregatedWellbeingSignal;
use App\Models\Department;
use App\Services\HumanOps\HumanOpsDataService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Index extends Component
{
    public array $headers = [
        ['index' => 'department', 'label' => 'Department'],
        ['index' => 'period_end', 'label' => 'Period End'],
        ['index' => 'participants', 'label' => 'Participants'],
        ['index' => 'confidence', 'label' => 'Confidence'],
        ['index' => 'avg_stress', 'label' => 'Avg Stress'],
        ['index' => 'avg_energy', 'label' => 'Avg Energy'],
        ['index' => 'mood_index', 'label' => 'Mood Index'],
        ['index' => 'wellness_score', 'label' => 'Wellness'],
    ];

    public ?array $org = null;

    public function mount(HumanOpsDataService $data): void
    {
        $data->logView('wellbeing');

        $latestOrg = AggregatedWellbeingSignal::whereNull('department_id')->latest('period_end')->first();
        if ($latestOrg) {
            $this->org = [
                'period_end' => $latestOrg->period_end?->format('M d, Y'),
                'participants' => (int)$latestOrg->participant_count,
                'confidence' => (float)$latestOrg->data_confidence,
                'avg_stress' => $latestOrg->avg_stress,
                'avg_energy' => $latestOrg->avg_energy,
                'mood_index' => $latestOrg->mood_index,
                'wellness_score' => $this->wellnessScore((float)$latestOrg->avg_stress, (float)$latestOrg->avg_energy),
            ];
        }
    }

    #[Computed]
    public function rows(): array
    {
        $departments = Department::query()->where('is_active', true)->orderBy('name')->get();

        return $departments->map(function (Department $dept) {
            $latest = AggregatedWellbeingSignal::where('department_id', $dept->id)
                ->latest('period_end')
                ->first();

            if (!$latest) {
                return [
                    'department' => $dept->name,
                    'period_end' => '—',
                    'participants' => '—',
                    'confidence' => '—',
                    'avg_stress' => '—',
                    'avg_energy' => '—',
                    'mood_index' => '—',
                    'wellness_score' => '—',
                ];
            }

            return [
                'department' => $dept->name,
                'period_end' => $latest->period_end?->format('M d, Y') ?? '—',
                'participants' => (int)$latest->participant_count,
                'confidence' => round(((float)$latest->data_confidence) * 100) . '%',
                'avg_stress' => $latest->avg_stress,
                'avg_energy' => $latest->avg_energy,
                'mood_index' => $latest->mood_index,
                'wellness_score' => $this->wellnessScore((float)$latest->avg_stress, (float)$latest->avg_energy),
            ];
        })->toArray();
    }

    private function wellnessScore(float $stress, float $energy): float
    {
        // Same formula used elsewhere in HumanOps
        return round((($energy + (6 - $stress)) / 2) * 2, 1);
    }

    public function render(): View
    {
        return view('livewire.humanops.wellbeing.index');
    }
}
