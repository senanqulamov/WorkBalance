<?php

namespace App\Policies;

use App\Models\TeamMetric;
use App\Models\User;

/**
 * Policy for TeamMetric access control.
 *
 * PRIVACY RULE: Only aggregated metrics (cohort size >= 5) are accessible.
 * Managers can only view metrics for their teams.
 */
class TeamMetricPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_team_metrics');
    }

    public function view(User $user, TeamMetric $metric): bool
    {
        // PRIVACY: Enforce minimum cohort size
        if ($metric->cohort_size < 5) {
            return false; // Too small, violates privacy threshold
        }

        if ($user->isAdmin() || $user->isOwner()) {
            return true;
        }

        // Managers can only view metrics for their teams
        if ($user->isManager()) {
            return $metric->team->manager_id === $user->id;
        }

        return false;
    }
}
