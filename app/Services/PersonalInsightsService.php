<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\PersonalInsight;
use App\Models\User;
use App\Models\WellBeingToolUsage;

class PersonalInsightsService
{
    /**
     * Generate personal insights for a specific user
     */
    public function generateInsightsForUser(User $user): void
    {
        // Only generate for employees
        if ($user->role !== 'employee') {
            return;
        }

        $checkIns = CheckIn::where('user_id', $user->id)
            ->where('check_in_date', '>=', now()->subDays(14))
            ->orderBy('check_in_date')
            ->get();

        if ($checkIns->count() < 3) {
            // Not enough data for meaningful insights
            return;
        }

        // Detect stress patterns
        $this->detectStressPattern($user, $checkIns);

        // Detect energy patterns
        $this->detectEnergyPattern($user, $checkIns);

        // Detect recovery signals
        $this->detectRecoverySignals($user, $checkIns);

        // Detect midweek dips
        $this->detectMidweekPattern($user, $checkIns);

        // Check tool usage patterns
        $this->analyzeToolUsage($user);
    }

    protected function detectStressPattern(User $user, $checkIns): void
    {
        $recentStress = $checkIns->slice(-7)->avg('stress_level');
        $olderStress = $checkIns->slice(0, 7)->avg('stress_level');

        // Don't create duplicate recent insights
        $existingInsight = PersonalInsight::where('user_id', $user->id)
            ->where('insight_type', 'stress_pattern')
            ->where('generated_at', '>=', now()->subDays(7))
            ->first();

        if ($existingInsight) return;

        if ($recentStress > 7 && $recentStress > $olderStress + 1) {
            PersonalInsight::create([
                'user_id' => $user->id,
                'insight_type' => 'stress_pattern',
                'title' => 'Your stress levels have been climbing',
                'description' => sprintf(
                    'Your recent stress has averaged %.1f/10, up from %.1f/10 previously. This is a normal response to increased demands. Consider using the grounding or breathing tools when stress feels overwhelming.',
                    $recentStress,
                    $olderStress
                ),
                'insight_data' => [
                    'recent_avg' => round($recentStress, 1),
                    'previous_avg' => round($olderStress, 1),
                ],
                'generated_at' => now(),
            ]);
        } elseif ($recentStress < 5 && $olderStress > 7) {
            PersonalInsight::create([
                'user_id' => $user->id,
                'insight_type' => 'stress_pattern',
                'title' => 'Your stress is coming down',
                'description' => sprintf(
                    'Good news—your stress has dropped from %.1f/10 to %.1f/10. Whatever you\'re doing (or stopped doing) seems to be helping.',
                    $olderStress,
                    $recentStress
                ),
                'insight_data' => [
                    'recent_avg' => round($recentStress, 1),
                    'previous_avg' => round($olderStress, 1),
                ],
                'generated_at' => now(),
            ]);
        }
    }

    protected function detectEnergyPattern(User $user, $checkIns): void
    {
        $avgEnergy = $checkIns->avg('energy_level');

        $existingInsight = PersonalInsight::where('user_id', $user->id)
            ->where('insight_type', 'energy_pattern')
            ->where('generated_at', '>=', now()->subDays(7))
            ->first();

        if ($existingInsight) return;

        if ($avgEnergy < 4) {
            PersonalInsight::create([
                'user_id' => $user->id,
                'insight_type' => 'energy_pattern',
                'title' => 'Your energy has been consistently low',
                'description' => sprintf(
                    'You\'ve been averaging %.1f/10 on energy lately. Low energy doesn\'t mean you\'re failing—it might mean you need more recovery time, clearer boundaries, or lighter workload for a bit.',
                    $avgEnergy
                ),
                'insight_data' => [
                    'avg_energy' => round($avgEnergy, 1),
                ],
                'generated_at' => now(),
            ]);
        } elseif ($avgEnergy > 7) {
            PersonalInsight::create([
                'user_id' => $user->id,
                'insight_type' => 'energy_pattern',
                'title' => 'You\'re feeling energized',
                'description' => sprintf(
                    'Your energy has been strong at %.1f/10. This usually means good sleep, clear goals, and sustainable pacing. Keep it up!',
                    $avgEnergy
                ),
                'insight_data' => [
                    'avg_energy' => round($avgEnergy, 1),
                ],
                'generated_at' => now(),
            ]);
        }
    }

    protected function detectRecoverySignals(User $user, $checkIns): void
    {
        // Look for improvement after low periods
        $sorted = $checkIns->sortBy('check_in_date')->values();

        if ($sorted->count() < 7) return;

        $firstHalf = $sorted->slice(0, (int)floor($sorted->count() / 2));
        $secondHalf = $sorted->slice((int)floor($sorted->count() / 2));

        $firstHalfWellness = ($firstHalf->avg('energy_level') + (11 - $firstHalf->avg('stress_level')) + $firstHalf->avg('mood_level')) / 3;
        $secondHalfWellness = ($secondHalf->avg('energy_level') + (11 - $secondHalf->avg('stress_level')) + $secondHalf->avg('mood_level')) / 3;

        $existingInsight = PersonalInsight::where('user_id', $user->id)
            ->where('insight_type', 'recovery_signal')
            ->where('generated_at', '>=', now()->subDays(7))
            ->first();

        if ($existingInsight) return;

        if ($firstHalfWellness < 5 && $secondHalfWellness > 6.5) {
            PersonalInsight::create([
                'user_id' => $user->id,
                'insight_type' => 'recovery_signal',
                'title' => 'You recover well after tough periods',
                'description' => 'After a challenging stretch, your well-being bounced back. This resilience is a strength—and it\'s okay to protect it by saying no sometimes.',
                'insight_data' => [
                    'improvement' => round($secondHalfWellness - $firstHalfWellness, 1),
                ],
                'generated_at' => now(),
            ]);
        }
    }

    protected function detectMidweekPattern(User $user, $checkIns): void
    {
        // Group by day of week
        $byDayOfWeek = $checkIns->groupBy(fn($checkIn) => $checkIn->check_in_date->dayOfWeek);

        // Wednesday = 3, Thursday = 4
        $midweekEnergy = collect([]);
        if ($byDayOfWeek->has(3)) $midweekEnergy = $midweekEnergy->concat($byDayOfWeek[3]);
        if ($byDayOfWeek->has(4)) $midweekEnergy = $midweekEnergy->concat($byDayOfWeek[4]);

        if ($midweekEnergy->isEmpty()) return;

        $avgMidweekEnergy = $midweekEnergy->avg('energy_level');
        $overallEnergy = $checkIns->avg('energy_level');

        $existingInsight = PersonalInsight::where('user_id', $user->id)
            ->where('insight_type', 'midweek_dip')
            ->where('generated_at', '>=', now()->subDays(14))
            ->first();

        if ($existingInsight) return;

        if ($avgMidweekEnergy < $overallEnergy - 1.5) {
            PersonalInsight::create([
                'user_id' => $user->id,
                'insight_type' => 'midweek_dip',
                'title' => 'Your energy tends to dip midweek',
                'description' => 'Wednesdays and Thursdays are harder for you. This is super common. Consider blocking off lighter work or recovery time on those days.',
                'insight_data' => [
                    'midweek_avg' => round($avgMidweekEnergy, 1),
                    'overall_avg' => round($overallEnergy, 1),
                ],
                'generated_at' => now(),
            ]);
        }
    }

    protected function analyzeToolUsage(User $user): void
    {
        $toolUsage = WellBeingToolUsage::query()
            ->with('tool')
            ->where('user_id', $user->id)
            ->where('used_at', '>=', now()->subDays(14))
            ->get();

        if ($toolUsage->isEmpty()) {
            return;
        }

        $completedTools = $toolUsage->where('completed', true);

        $existingInsight = PersonalInsight::where('user_id', $user->id)
            ->where('insight_type', 'tool_usage')
            ->where('generated_at', '>=', now()->subDays(14))
            ->first();

        if ($existingInsight) {
            return;
        }

        if ($completedTools->count() >= 3) {
            $mostUsed = $toolUsage
                ->groupBy('tool_id')
                ->sortByDesc(fn($group) => $group->count())
                ->keys()
                ->first();

            $toolName = $mostUsed
                ? ($toolUsage->firstWhere('tool_id', $mostUsed)?->tool?->title ?? 'a tool')
                : 'a tool';

            PersonalInsight::create([
                'user_id' => $user->id,
                'insight_type' => 'tool_usage',
                'title' => 'You\'re actively using well-being tools',
                'description' => sprintf(
                    'You\'ve used tools %d times recently, especially "%s". This kind of self-awareness and self-care makes a real difference.',
                    $completedTools->count(),
                    $toolName
                ),
                'insight_data' => [
                    'total_uses' => $completedTools->count(),
                    'most_used_tool_id' => $mostUsed,
                    'most_used_tool' => $toolName,
                ],
                'generated_at' => now(),
            ]);
        }
    }

    /**
     * Generate insights for all active employees
     */
    public function generateInsightsForAllEmployees(): void
    {
        $employees = User::where('role', 'employee')
            ->where('is_active', true)
            ->get();

        foreach ($employees as $employee) {
            try {
                $this->generateInsightsForUser($employee);
            } catch (\Exception $e) {
                // Log error but continue with other employees
                logger()->error('Failed to generate insights for user ' . $employee->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
