<?php

namespace App\Livewire\HumanOps\Departments;

use App\Models\AggregatedWellbeingSignal;
use App\Models\Department;
use App\Services\HumanOps\HumanOpsDataService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public array $departments = [];

    public function mount(HumanOpsDataService $data): void
    {
        $data->logView('departments');
        $this->loadDepartments();
    }

    protected function loadDepartments(): void
    {
        $depts = Department::where('is_active', true)->get();

        $this->departments = $depts->map(function (Department $dept) {
            $latest = AggregatedWellbeingSignal::where('department_id', $dept->id)
                ->latest('period_end')
                ->first();

            if (!$latest) {
                return [
                    'name' => $dept->name,
                    'code' => $dept->code,
                    'insufficient_data' => true,
                    'message' => 'Collecting baseline data...',
                ];
            }

            // Convert to a 0–10-ish wellbeing score for display
            $stress = (float)($latest->avg_stress ?? 3);
            $energy = (float)($latest->avg_energy ?? 3);
            $wellnessScore = round((($energy + (6 - $stress)) / 2) * 2, 2);

            $status = $wellnessScore >= 7 ? 'healthy' : ($wellnessScore >= 5 ? 'monitor' : 'support_needed');

            return [
                'name' => $dept->name,
                'code' => $dept->code,
                'period_end' => $latest->period_end?->format('M d, Y'),
                'participant_count' => $latest->participant_count,
                'confidence' => $latest->data_confidence,
                'wellness_score' => $wellnessScore,
                'avg_stress' => $latest->avg_stress,
                'avg_energy' => $latest->avg_energy,
                'mood_index' => $latest->mood_index,
                'status' => $status,
            ];
        })->filter()->values()->toArray();
    }

    public function render(): View
    {
        return view('livewire.humanops.departments.index');
    }
}
