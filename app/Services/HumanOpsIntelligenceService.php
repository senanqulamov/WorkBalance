<?php

namespace App\Services;

use App\Models\ActionRecommendation;
use App\Models\CheckIn;
use App\Models\Department;
use App\Models\OrganizationHealthIndex;
use App\Models\RiskSignal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HumanOpsIntelligenceService
{
    /**
     * Calculate and store daily organization health index
     */
    public function calculateDailyHealthIndex(): OrganizationHealthIndex
    {
        $date = now()->subDays(2)->startOfDay(); // 48-hour delay

        // Get all check-ins from that day
        $checkIns = CheckIn::where('check_in_date', $date->format('Y-m-d'))->get();

        if ($checkIns->count() < 10) {
            // Not enough data for privacy protection
            return $this->createLowConfidenceIndex($date);
        }

        // Calculate overall wellbeing
        $avgStress = $checkIns->avg('stress_level');
        $avgEnergy = $checkIns->avg('energy_level');
        $avgMood = $checkIns->avg('mood_level');

        $inverseStress = 11 - $avgStress;
        $wellbeingScore = ($inverseStress + $avgEnergy + $avgMood) / 3;

        // Calculate burnout risk (high stress + low energy)
        $burnoutRisk = ($avgStress / 10 + (11 - $avgEnergy) / 10) / 2;

        // Calculate energy depletion
        $energyDepletion = (11 - $avgEnergy) / 10;

        // Financial stress proxy (stress levels above 7)
        $highStressCount = $checkIns->where('stress_level', '>', 7)->count();
        $financialStress = $checkIns->count() > 0 ? $highStressCount / $checkIns->count() : 0;

        // Relationship health (mood as proxy)
        $relationshipHealth = $avgMood / 10;

        // Determine trend
        $previousIndex = OrganizationHealthIndex::where('date', '<', $date)
            ->latest('date')
            ->first();

        $trend = 'stable';
        if ($previousIndex) {
            $diff = $wellbeingScore - $previousIndex->overall_wellbeing_score;
            if ($diff > 0.5) $trend = 'improving';
            elseif ($diff < -0.5) $trend = 'declining';
        }

        return OrganizationHealthIndex::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'overall_wellbeing_score' => round($wellbeingScore, 2),
                'burnout_risk_level' => round($burnoutRisk, 2),
                'financial_stress_level' => round($financialStress, 2),
                'relationship_health_score' => round($relationshipHealth, 2),
                'energy_depletion_score' => round($energyDepletion, 2),
                'total_participants' => $checkIns->pluck('user_id')->unique()->count(),
                'confidence_level' => $this->calculateConfidence($checkIns->count()),
                'trend_direction' => $trend,
            ]
        );
    }

    /**
     * Detect and create risk signals
     */
    public function detectRiskSignals(): void
    {
        $departments = Department::where('is_active', true)->get();

        foreach ($departments as $dept) {
            $employeeIds = $dept->users->where('role', 'employee')->pluck('id');

            if ($employeeIds->count() < 10) continue; // Privacy protection

            $recentCheckIns = CheckIn::whereIn('user_id', $employeeIds)
                ->where('check_in_date', '>=', now()->subDays(9)) // 7 days + 48h delay
                ->where('check_in_date', '<=', now()->subDays(2))
                ->get();

            if ($recentCheckIns->isEmpty()) continue;

            // Detect burnout risk (high stress + low energy)
            $avgStress = $recentCheckIns->avg('stress_level');
            $avgEnergy = $recentCheckIns->avg('energy_level');

            if ($avgStress > 7 && $avgEnergy < 4) {
                $this->createOrUpdateSignal(
                    'burnout_risk',
                    'elevated',
                    $dept->code,
                    $employeeIds->count(),
                    ($avgStress / 10 + (11 - $avgEnergy) / 10) / 2,
                    sprintf(
                        'Burnout risk detected in %s. Team shows elevated stress (%.1f/10) combined with low energy (%.1f/10) over the past week.',
                        $dept->name,
                        $avgStress,
                        $avgEnergy
                    )
                );
            }

            // Detect energy depletion
            if ($avgEnergy < 4) {
                $this->createOrUpdateSignal(
                    'energy_depletion',
                    $avgEnergy < 3 ? 'elevated' : 'moderate',
                    $dept->code,
                    $employeeIds->count(),
                    (11 - $avgEnergy) / 10,
                    sprintf(
                        'Energy depletion pattern in %s. Team energy levels are consistently low (%.1f/10), suggesting fatigue or overwork.',
                        $dept->name,
                        $avgEnergy
                    )
                );
            }

            // Detect relationship strain (low mood)
            $avgMood = $recentCheckIns->avg('mood_level');
            if ($avgMood < 5) {
                $this->createOrUpdateSignal(
                    'relationship_strain',
                    'moderate',
                    $dept->code,
                    $employeeIds->count(),
                    (11 - $avgMood) / 10,
                    sprintf(
                        'Relationship or morale concerns in %s. Team mood is below healthy levels (%.1f/10), which may indicate communication or collaboration issues.',
                        $dept->name,
                        $avgMood
                    )
                );
            }
        }
    }

    /**
     * Generate action recommendations
     */
    public function generateRecommendations(): void
    {
        // Get active risk signals
        $activeSignals = RiskSignal::whereNull('resolved_at')
            ->where('detected_at', '>=', now()->subDays(7))
            ->get();

        foreach ($activeSignals as $signal) {
            // Check if recommendation already exists
            $existing = ActionRecommendation::where('department_code', $signal->department_code)
                ->where('recommendation_type', $signal->signal_type)
                ->whereNull('implemented_at')
                ->where('generated_at', '>=', now()->subDays(14))
                ->first();

            if ($existing) continue; // Don't duplicate recent recommendations

            $recommendation = $this->buildRecommendation($signal);

            if ($recommendation) {
                ActionRecommendation::create($recommendation);
            }
        }

        // Generate positive pattern recognitions
        $this->generatePositiveRecognitions();
    }

    /**
     * Build recommendation from signal
     */
    protected function buildRecommendation(RiskSignal $signal): ?array
    {
        $dept = $signal->department;
        if (!$dept) return null;

        return match($signal->signal_type) {
            'burnout_risk' => [
                'recommendation_type' => 'workload_review',
                'priority' => $signal->severity === 'elevated' ? 'high' : 'medium',
                'target_scope' => 'department',
                'title' => sprintf('Review Workload Distribution in %s', $dept->name),
                'description' => sprintf(
                    'Burnout risk indicators suggest %s may be experiencing sustained high stress and low energy. This pattern typically emerges from workload imbalances, unclear priorities, or insufficient resources.',
                    $dept->name
                ),
                'suggested_actions' => [
                    'Review current project deadlines and consider extensions where feasible',
                    'Audit workload distribution to identify bottlenecks or overloaded individuals',
                    'Ensure managers are having regular supportive 1-on-1s (not just status updates)',
                    'Consider bringing in temporary support or redistributing non-critical work',
                    'Communicate clearly about priorities to reduce decision fatigue',
                ],
                'evidence_summary' => sprintf(
                    'Based on aggregated check-in data from %d employees over 7 days. Signal strength: %.0f%%',
                    $signal->affected_group_size,
                    $signal->signal_strength * 100
                ),
                'department_code' => $signal->department_code,
                'generated_at' => now(),
            ],

            'energy_depletion' => [
                'recommendation_type' => 'work_rhythm',
                'priority' => 'medium',
                'target_scope' => 'department',
                'title' => sprintf('Support Work-Life Balance in %s', $dept->name),
                'description' => sprintf(
                    'Team members in %s are reporting consistently low energy levels. This suggests insufficient recovery time, meeting overload, or sustained intensity without breaks.',
                    $dept->name
                ),
                'suggested_actions' => [
                    'Review meeting schedules - consider no-meeting blocks or meeting-free days',
                    'Encourage use of breaks and time off',
                    'Check for after-hours work patterns that may indicate poor boundaries',
                    'Assess whether deadlines allow for sustainable pacing',
                    'Consider flexible work arrangements if not already available',
                ],
                'evidence_summary' => sprintf(
                    'Based on energy level patterns from %d employees. Signal strength: %.0f%%',
                    $signal->affected_group_size,
                    $signal->signal_strength * 100
                ),
                'department_code' => $signal->department_code,
                'generated_at' => now(),
            ],

            'relationship_strain' => [
                'recommendation_type' => 'communication',
                'priority' => 'medium',
                'target_scope' => 'department',
                'title' => sprintf('Strengthen Team Dynamics in %s', $dept->name),
                'description' => sprintf(
                    'Mood indicators in %s suggest potential communication or collaboration challenges. This may reflect unresolved conflicts, unclear expectations, or insufficient team cohesion.',
                    $dept->name
                ),
                'suggested_actions' => [
                    'Check in with team leads about any known interpersonal tensions',
                    'Consider facilitated team retrospectives or working agreements sessions',
                    'Ensure role clarity and decision-making authority are well-defined',
                    'Create space for informal connection (team lunches, coffee chats)',
                    'Review communication channels for effectiveness and inclusivity',
                ],
                'evidence_summary' => sprintf(
                    'Based on mood patterns from %d employees. Signal strength: %.0f%%',
                    $signal->affected_group_size,
                    $signal->signal_strength * 100
                ),
                'department_code' => $signal->department_code,
                'generated_at' => now(),
            ],

            default => null,
        };
    }

    /**
     * Generate positive pattern recognitions
     */
    protected function generatePositiveRecognitions(): void
    {
        $departments = Department::where('is_active', true)->get();

        foreach ($departments as $dept) {
            $employeeIds = $dept->users->where('role', 'employee')->pluck('id');

            if ($employeeIds->count() < 10) continue;

            $recentCheckIns = CheckIn::whereIn('user_id', $employeeIds)
                ->where('check_in_date', '>=', now()->subDays(9))
                ->where('check_in_date', '<=', now()->subDays(2))
                ->get();

            if ($recentCheckIns->isEmpty()) continue;

            $avgEnergy = $recentCheckIns->avg('energy_level');
            $avgStress = $recentCheckIns->avg('stress_level');

            // High energy + moderate stress = healthy challenge
            if ($avgEnergy >= 7 && $avgStress < 6) {
                ActionRecommendation::firstOrCreate(
                    [
                        'department_code' => $dept->code,
                        'recommendation_type' => 'positive_pattern',
                        'generated_at' => now()->startOfDay(),
                    ],
                    [
                        'priority' => 'positive',
                        'target_scope' => 'department',
                        'title' => sprintf('Strong Well-Being Patterns in %s', $dept->name),
                        'description' => sprintf(
                            '%s shows healthy energy levels and manageable stress. This indicates good work-life balance, clear expectations, and sustainable pacing.',
                            $dept->name
                        ),
                        'suggested_actions' => [
                            'Document what\'s working well in this team',
                            'Consider sharing practices with other departments',
                            'Recognize leadership for creating sustainable conditions',
                            'Maintain these patterns even during busy periods',
                        ],
                        'evidence_summary' => sprintf(
                            'Based on consistently positive signals from %d employees.',
                            $employeeIds->count()
                        ),
                    ]
                );
            }
        }
    }

    /**
     * Create or update risk signal
     */
    protected function createOrUpdateSignal(
        string $type,
        string $severity,
        string $deptCode,
        int $groupSize,
        float $strength,
        string $description
    ): void {
        $existing = RiskSignal::where('signal_type', $type)
            ->where('department_code', $deptCode)
            ->whereNull('resolved_at')
            ->first();

        if ($existing) {
            // Update existing signal
            $existing->update([
                'severity' => $severity,
                'signal_strength' => round($strength, 2),
                'description' => $description,
                'affected_group_size' => $groupSize,
            ]);
        } else {
            // Create new signal
            RiskSignal::create([
                'signal_type' => $type,
                'severity' => $severity,
                'department_code' => $deptCode,
                'affected_group_size' => $groupSize,
                'signal_strength' => round($strength, 2),
                'description' => $description,
                'detected_at' => now(),
            ]);
        }
    }

    /**
     * Calculate confidence based on sample size
     */
    protected function calculateConfidence(int $sampleSize): float
    {
        if ($sampleSize < 10) return 0.0;
        if ($sampleSize < 30) return 0.5;
        if ($sampleSize < 50) return 0.7;
        if ($sampleSize < 100) return 0.85;
        return 1.0;
    }

    /**
     * Create low confidence index when insufficient data
     */
    protected function createLowConfidenceIndex($date): OrganizationHealthIndex
    {
        return OrganizationHealthIndex::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'overall_wellbeing_score' => 0,
                'burnout_risk_level' => 0,
                'financial_stress_level' => 0,
                'relationship_health_score' => 0,
                'energy_depletion_score' => 0,
                'total_participants' => 0,
                'confidence_level' => 0,
                'trend_direction' => 'stable',
            ]
        );
    }
}
