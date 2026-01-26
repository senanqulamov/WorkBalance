<?php

namespace App\Services;

use App\Models\EmotionalCheckIn;
use App\Models\Team;
use App\Models\TeamMetric;
use App\Models\TherapeuticSession;
use Illuminate\Support\Facades\DB;

/**
 * Service for aggregating employee wellbeing data into team metrics.
 *
 * PRIVACY: All aggregation enforces minimum cohort size (≥5).
 * Individual data is NEVER exposed.
 */
class TeamMetricsAggregationService
{
    private int $minimumCohortSize = 5;

    public function aggregateTeamMetrics(Team $team, \DateTime $date): ?TeamMetric
    {
        $employees = $team->employees;
        $cohortSize = $employees->count();

        // PRIVACY ENFORCEMENT: Require minimum cohort size
        if ($cohortSize < $this->minimumCohortSize) {
            return null;
        }

        $dateString = $date->format('Y-m-d');

        // Aggregate check-ins for this date
        $checkIns = EmotionalCheckIn::whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('checked_in_at', $dateString)
            ->get();

        if ($checkIns->isEmpty()) {
            return null;
        }

        // Calculate aggregated metrics
        $avgStressLevel = round($checkIns->avg('stress_level'), 2);
        $avgMoodLevel = round($checkIns->avg('mood_level'), 2);
        $avgEnergyLevel = round($checkIns->avg('energy_level'), 2);
        $checkInParticipation = round(($checkIns->count() / $cohortSize) * 100, 2);

        // Determine stress trend
        $stressTrend = $this->calculateStressTrend($team, $avgStressLevel, $date);

        // Determine burnout risk
        $burnoutRisk = $this->calculateBurnoutRisk($avgStressLevel, $avgEnergyLevel, $avgMoodLevel);

        // Count completed therapeutic sessions
        $pathsCompleted = TherapeuticSession::whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('completed_at', $dateString)
            ->where('status', 'completed')
            ->count();

        // Calculate average intensity shift
        $intensityShift = TherapeuticSession::whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('completed_at', $dateString)
            ->where('status', 'completed')
            ->whereNotNull('intensity_after')
            ->selectRaw('AVG(intensity_before - intensity_after) as avg_shift')
            ->value('avg_shift');

        return TeamMetric::updateOrCreate(
            [
                'team_id' => $team->id,
                'metric_date' => $dateString,
            ],
            [
                'cohort_size' => $cohortSize,
                'stress_trend' => $stressTrend,
                'engagement_rate' => $checkInParticipation,
                'burnout_risk_level' => $burnoutRisk,
                'check_in_participation' => $checkInParticipation,
                'paths_completed' => $pathsCompleted,
                'average_intensity_shift' => $intensityShift ? round($intensityShift, 2) : null,
            ]
        );
    }

    private function calculateStressTrend(Team $team, float $currentStress, \DateTime $date): string
    {
        // Compare to previous 7 days average
        $startDate = (clone $date)->modify('-7 days')->format('Y-m-d');
        $endDate = (clone $date)->modify('-1 day')->format('Y-m-d');

        $previousMetrics = TeamMetric::where('team_id', $team->id)
            ->whereBetween('metric_date', [$startDate, $endDate])
            ->get();

        if ($previousMetrics->isEmpty()) {
            return 'steady';
        }

        $previousAvgStress = $previousMetrics->avg(function ($metric) {
            return $this->stressLevelFromTrend($metric->stress_trend);
        });

        $diff = $currentStress - $previousAvgStress;
        $volatility = $previousMetrics->pluck('stress_trend')->unique()->count();

        if ($volatility > 2) {
            return 'volatile';
        }

        if ($diff <= -0.5) {
            return 'cooling';
        } elseif ($diff >= 0.5) {
            return 'rising';
        } else {
            return 'steady';
        }
    }

    private function stressLevelFromTrend(string $trend): float
    {
        return match($trend) {
            'cooling' => 2.0,
            'steady' => 3.0,
            'rising' => 4.0,
            'volatile' => 4.5,
            default => 3.0,
        };
    }

    private function calculateBurnoutRisk(float $stress, float $energy, float $mood): string
    {
        // Simple heuristic: high stress + low energy/mood = high risk
        $riskScore = ($stress * 2) - $energy - $mood;

        if ($riskScore >= 5) {
            return 'high';
        } elseif ($riskScore >= 3) {
            return 'elevated';
        } elseif ($riskScore >= 1) {
            return 'moderate';
        } else {
            return 'low';
        }
    }
}
