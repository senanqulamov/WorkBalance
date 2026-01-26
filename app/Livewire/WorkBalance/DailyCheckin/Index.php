<?php

namespace App\Livewire\WorkBalance\DailyCheckin;

use App\Models\CheckInReflection;
use App\Models\DailyCheckIn;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    // Stored as 1-5 values (matches DB comment + AggregationService expectations)
    public int $stressLevel = 3;
    public int $energyLevel = 3;

    // Stored as a mood_state string in DB
    public string $moodState = 'okay';

    // Stored as optional_note on the check-in
    public string $optionalNote = '';

    // Stored in check_in_reflections table
    public string $reflectionText = '';

    public bool $showSuccess = false;
    public bool $alreadyCheckedInToday = false;

    public array $availableMoods = ['great', 'good', 'okay', 'low', 'struggling'];

    public function mount(): void
    {
        $todayCheckIn = DailyCheckIn::where('user_id', auth()->id())
            ->where('check_in_date', today())
            ->first();

        if ($todayCheckIn) {
            $this->alreadyCheckedInToday = true;
            $this->stressLevel = (int)($todayCheckIn->stress_value ?? 3);
            $this->energyLevel = (int)($todayCheckIn->energy_value ?? 3);
            $this->moodState = (string)($todayCheckIn->mood_state ?? 'okay');
            $this->optionalNote = (string)($todayCheckIn->optional_note ?? '');

            $reflection = CheckInReflection::where('user_id', auth()->id())
                ->where('related_check_in_id', $todayCheckIn->id)
                ->latest('id')
                ->first();

            $this->reflectionText = (string)($reflection?->reflection_text ?? '');
        }
    }

    public function render(): View
    {
        return view('livewire.workbalance.daily-checkin.index');
    }

    public function submit(): void
    {
        $this->validate([
            'stressLevel' => 'required|integer|min:1|max:5',
            'energyLevel' => 'required|integer|min:1|max:5',
            'moodState' => 'required|string|in:great,good,okay,low,struggling',
            'optionalNote' => 'nullable|string|max:500',
            'reflectionText' => 'nullable|string|max:1000',
        ]);

        $checkIn = DailyCheckIn::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'check_in_date' => today(),
            ],
            [
                'stress_level' => $this->levelToEnum($this->stressLevel),
                'stress_value' => $this->stressLevel,
                'energy_level' => $this->levelToEnum($this->energyLevel),
                'energy_value' => $this->energyLevel,
                'mood_state' => $this->moodState,
                'optional_note' => $this->optionalNote ?: null,
            ]
        );

        if (trim($this->reflectionText) !== '') {
            CheckInReflection::create([
                'user_id' => auth()->id(),
                'reflection_text' => trim($this->reflectionText),
                'related_check_in_id' => $checkIn->id,
            ]);
        }

        $this->showSuccess = true;
        $this->alreadyCheckedInToday = true;

        $this->dispatch('checkin-submitted');
        $this->dispatch('show-success-redirect');
    }

    private function levelToEnum(int $value): string
    {
        return match (true) {
            $value <= 2 => 'low',
            $value === 3 => 'medium',
            default => 'high',
        };
    }
}
