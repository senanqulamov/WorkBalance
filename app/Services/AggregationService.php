<?php

namespace App\Services;

use App\Models\AggregatedWellbeingSignal;
use App\Models\AggregationExport;
use App\Models\BurnoutRiskSignal;
use App\Models\DailyCheckIn;
use App\Models\Department;
use App\Models\EmployeePrivacySetting;
use App\Models\FinancialStressSignal;
use App\Models\PrivacyAuditLog;
use App\Models\RelationshipHealthSignal;
use App\Models\TrendSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AggregationService
{
    // Privacy constants
    const MIN_GROUP_SIZE = 7; // Minimum participants for privacy
    const DELAY_HOURS = 48; // Time delay before aggregation

    /**
     * Run weekly aggregation for all departments
     */
    public function runWeeklyAggregation(): void
    {
        $periodEnd = now()->subHours(self::DELAY_HOURS)->startOfWeek();
        $periodStart = $periodEnd->copy()->subWeek();

        $departments = Department::where('is_active', true)->get();

        foreach ($departments as $department) {
            $this->aggregateDepartment($department, 'weekly', $periodStart, $periodEnd);
        }
    }

    /**
     * Aggregate data for a specific department
     */
    protected function aggregateDepartment(
        Department $department,
        string $period,
        Carbon $periodStart,
        Carbon $periodEnd
    ): ?AggregatedWellbeingSignal {

        // Step 1: Create export record
        $export = AggregationExport::create([
            'period' => $period,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'department_id' => $department->id,
            'exported_at' => now(),
            'status' => 'pending',
        ]);

        // Step 2: Get eligible employees (consent check)
        $eligibleUserIds = $this->getEligibleEmployees($department);

        // Step 3: Privacy check - minimum group size
        if ($eligibleUserIds->count() < self::MIN_GROUP_SIZE) {
            $this->logPrivacyFailure($export, 'Insufficient group size', $eligibleUserIds->count());
            $export->update(['status' => 'failed']);
            return null;
        }

        // Step 4: Get sanitized check-ins (NO PERSONAL DATA)
        $checkIns = DailyCheckIn::whereIn('user_id', $eligibleUserIds)
            ->whereBetween('check_in_date', [$periodStart, $periodEnd])
            ->get();

        if ($checkIns->isEmpty()) {
            $this->logPrivacyFailure($export, 'No data available', 0);
            $export->update(['status' => 'failed']);
            return null;
        }

        // Step 5: Calculate aggregated metrics (SANITIZED)
        $avgStress = $this->normalizeStressLevel($checkIns->avg('stress_value'));
        $avgEnergy = $this->normalizeEnergyLevel($checkIns->avg('energy_value'));
        $moodIndex = $this->calculateMoodIndex($checkIns);
        $confidence = $this->calculateConfidence($checkIns->count(), $eligibleUserIds->count());

        // Step 6: Create aggregated signal (NO USER IDS)
        $signal = AggregatedWellbeingSignal::create([
            'department_id' => $department->id,
            'period' => $period,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'avg_stress' => $avgStress,
            'avg_energy' => $avgEnergy,
            'mood_index' => $moodIndex,
            'data_confidence' => $confidence,
            'participant_count' => $checkIns->pluck('user_id')->unique()->count(),
            'calculated_at' => now(),
        ]);

        // Step 7: Generate derived risk signals
        $this->generateRiskSignals($department, $signal);

        // Step 8: Create trend snapshots
        $this->createTrendSnapshots($department, $signal, $periodStart);

        // Step 9: Log successful privacy-compliant aggregation
        $this->logPrivacySuccess($export, $eligibleUserIds->count());

        $export->update([
            'status' => 'completed',
            'records_count' => $checkIns->count(),
            'min_group_size_met' => $eligibleUserIds->count(),
        ]);

        return $signal;
    }

    /**
     * Get employees who consented to aggregation
     */
    protected function getEligibleEmployees(Department $department)
    {
        return DB::table('users')
            ->join('employee_profiles', 'users.id', '=', 'employee_profiles.user_id')
            ->leftJoin('employee_privacy_settings', 'users.id', '=', 'employee_privacy_settings.user_id')
            ->where('employee_profiles.department_id', $department->id)
            ->where('users.role', 'employee')
            ->where('users.is_active', true)
            ->where(function($query) {
                $query->whereNull('employee_privacy_settings.allow_aggregation')
                      ->orWhere('employee_privacy_settings.allow_aggregation', true);
            })
            ->pluck('users.id');
    }

    /**
     * Normalize stress level (remove extremes)
     */
    protected function normalizeStressLevel(?float $value): ?float
    {
        if ($value === null) return null;
        return round(min(max($value, 1), 5), 2); // Clamp to 1-5 range
    }

    /**
     * Normalize energy level (remove extremes)
     */
    protected function normalizeEnergyLevel(?float $value): ?float
    {
        if ($value === null) return null;
        return round(min(max($value, 1), 5), 2); // Clamp to 1-5 range
    }

    /**
     * Calculate mood index from mood states
     */
    protected function calculateMoodIndex($checkIns): float
    {
        // Simple mapping: count positive vs negative moods
        $moodMapping = [
            'great' => 5,
            'good' => 4,
            'okay' => 3,
            'low' => 2,
            'struggling' => 1,
        ];

        $moodValues = $checkIns->pluck('mood_state')
            ->filter()
            ->map(fn($mood) => $moodMapping[strtolower($mood)] ?? 3);

        return round($moodValues->isEmpty() ? 3.0 : $moodValues->avg(), 2);
    }

    /**
     * Calculate confidence score based on participation
     */
    protected function calculateConfidence(int $checkInCount, int $eligibleCount): float
    {
        if ($eligibleCount === 0) return 0.0;

        $participationRate = $checkInCount / ($eligibleCount * 7); // Assuming weekly

        return round(min($participationRate, 1.0), 2);
    }

    /**
     * Generate risk signals from aggregated data
     */
    protected function generateRiskSignals(Department $department, AggregatedWellbeingSignal $signal): void
    {
        // Burnout risk: high stress + low energy
        if ($signal->avg_stress > 3.5 && $signal->avg_energy < 2.5) {
            BurnoutRiskSignal::create([
                'department_id' => $department->id,
                'risk_level' => $signal->avg_stress > 4 ? 'elevated' : 'moderate',
                'trend_direction' => $this->calculateTrend($department, 'stress'),
                'description' => "Department showing elevated stress with low energy patterns.",
                'signal_strength' => round(($signal->avg_stress / 5 + (5 - $signal->avg_energy) / 5) / 2, 2),
                'calculated_at' => now(),
            ]);
        }

        // Financial stress: consistently high stress
        if ($signal->avg_stress > 4.0) {
            FinancialStressSignal::create([
                'department_id' => $department->id,
                'stress_level' => 'high',
                'trend_direction' => $this->calculateTrend($department, 'stress'),
                'description' => "High stress levels may indicate workload or external pressures.",
                'calculated_at' => now(),
            ]);
        }

        // Relationship health: low mood + volatility
        if ($signal->mood_index < 2.5) {
            RelationshipHealthSignal::create([
                'department_id' => $department->id,
                'strain_level' => 'moderate',
                'volatility' => 0.5, // Placeholder - would calculate from variance
                'description' => "Team mood indicators suggest potential communication challenges.",
                'calculated_at' => now(),
            ]);
        }
    }

    /**
     * Calculate trend direction
     */
    protected function calculateTrend(Department $department, string $metric): string
    {
        $previous = AggregatedWellbeingSignal::where('department_id', $department->id)
            ->orderByDesc('period_start')
            ->skip(1)
            ->first();

        if (!$previous) return 'stable';

        $current = AggregatedWellbeingSignal::where('department_id', $department->id)
            ->latest('period_start')
            ->first();

        if (!$current) return 'stable';

        $field = 'avg_' . $metric;
        $diff = $current->$field - $previous->$field;

        if ($metric === 'stress') {
            return $diff > 0.3 ? 'declining' : ($diff < -0.3 ? 'improving' : 'stable');
        }

        return $diff > 0.3 ? 'improving' : ($diff < -0.3 ? 'declining' : 'stable');
    }

    /**
     * Create trend snapshots for historical comparison
     */
    protected function createTrendSnapshots(
        Department $department,
        AggregatedWellbeingSignal $signal,
        Carbon $periodStart
    ): void {
        foreach (['stress', 'energy', 'mood'] as $metric) {
            $field = $metric === 'mood' ? 'mood_index' : 'avg_' . $metric;

            TrendSnapshot::create([
                'scope' => 'department',
                'department_id' => $department->id,
                'metric' => $metric,
                'value' => $signal->$field,
                'period' => $signal->period,
                'period_start' => $periodStart,
            ]);
        }
    }

    /**
     * Log privacy failure
     */
    protected function logPrivacyFailure(AggregationExport $export, string $reason, int $actualSize): void
    {
        PrivacyAuditLog::create([
            'export_id' => $export->id,
            'rules_applied' => json_encode([
                'min_group_size' => self::MIN_GROUP_SIZE,
                'delay_hours' => self::DELAY_HOURS,
                'consent_check' => true,
            ]),
            'min_group_size' => self::MIN_GROUP_SIZE,
            'actual_group_size' => $actualSize,
            'delay_hours' => self::DELAY_HOURS,
            'passed' => false,
            'failure_reason' => $reason,
        ]);
    }

    /**
     * Log privacy success
     */
    protected function logPrivacySuccess(AggregationExport $export, int $actualSize): void
    {
        PrivacyAuditLog::create([
            'export_id' => $export->id,
            'rules_applied' => json_encode([
                'min_group_size' => self::MIN_GROUP_SIZE,
                'delay_hours' => self::DELAY_HOURS,
                'consent_check' => true,
                'sanitization' => true,
            ]),
            'min_group_size' => self::MIN_GROUP_SIZE,
            'actual_group_size' => $actualSize,
            'delay_hours' => self::DELAY_HOURS,
            'passed' => true,
        ]);
    }
}
