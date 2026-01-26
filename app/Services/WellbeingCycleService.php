<?php

namespace App\Services;

use App\Models\EmotionalCheckIn;
use App\Models\TherapeuticSession;
use App\Models\User;
use App\Models\WellbeingCycle;

/**
 * Service for managing employee wellbeing cycles and check-ins.
 *
 * PRIVACY: This service handles personal employee data.
 * Data NEVER flows to employer-facing features.
 */
class WellbeingCycleService
{
    public function startDailyCycle(User $employee): WellbeingCycle
    {
        // Check if employee already has an active cycle today
        $existingCycle = WellbeingCycle::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->whereDate('started_at', today())
            ->first();

        if ($existingCycle) {
            return $existingCycle;
        }

        return WellbeingCycle::create([
            'employee_id' => $employee->id,
            'started_at' => now(),
            'status' => 'active',
            'cycle_type' => 'daily',
        ]);
    }

    public function createCheckIn(User $employee, array $data): EmotionalCheckIn
    {
        $cycle = $this->startDailyCycle($employee);

        return EmotionalCheckIn::create([
            'wellbeing_cycle_id' => $cycle->id,
            'employee_id' => $employee->id,
            'mood_level' => $data['mood_level'],
            'energy_level' => $data['energy_level'],
            'stress_level' => $data['stress_level'],
            'private_note' => $data['private_note'] ?? null,
            'checked_in_at' => now(),
        ]);
    }

    public function startTherapeuticSession(User $employee, int $pathId, array $data): TherapeuticSession
    {
        $cycle = WellbeingCycle::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->latest('started_at')
            ->first();

        if (!$cycle) {
            $cycle = $this->startDailyCycle($employee);
        }

        return TherapeuticSession::create([
            'wellbeing_cycle_id' => $cycle->id,
            'employee_id' => $employee->id,
            'therapeutic_path_id' => $pathId,
            'situation_type' => $data['situation_type'] ?? null,
            'started_at' => now(),
            'status' => 'in_progress',
            'intensity_before' => $data['intensity_before'] ?? null,
        ]);
    }

    public function completeTherapeuticSession(TherapeuticSession $session, array $data): TherapeuticSession
    {
        $session->update([
            'completed_at' => now(),
            'status' => 'completed',
            'intensity_after' => $data['intensity_after'] ?? null,
            'reflection_note' => $data['reflection_note'] ?? null,
        ]);

        return $session;
    }

    public function getEmployeeProgress(User $employee, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $checkInsCount = EmotionalCheckIn::where('employee_id', $employee->id)
            ->where('checked_in_at', '>=', $startDate)
            ->count();

        $pathsCompleted = TherapeuticSession::where('employee_id', $employee->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->count();

        $avgIntensityShift = TherapeuticSession::where('employee_id', $employee->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', $startDate)
            ->whereNotNull('intensity_after')
            ->selectRaw('AVG(intensity_before - intensity_after) as avg_shift')
            ->value('avg_shift');

        return [
            'check_ins_count' => $checkInsCount,
            'paths_completed' => $pathsCompleted,
            'average_intensity_shift' => $avgIntensityShift ? round($avgIntensityShift, 2) : 0,
            'days_tracked' => $days,
        ];
    }
}
