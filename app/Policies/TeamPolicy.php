<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

/**
 * Policy for Team access control.
 * Managers can only view their own teams.
 * Owners and admins can view all teams.
 */
class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_teams');
    }

    public function view(User $user, Team $team): bool
    {
        if ($user->isAdmin() || $user->isOwner()) {
            return true;
        }

        // Managers can only view their own teams
        if ($user->isManager()) {
            return $team->manager_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_teams');
    }

    public function update(User $user, Team $team): bool
    {
        if ($user->isAdmin() || $user->isOwner()) {
            return true;
        }

        // Managers can only update their own teams
        if ($user->isManager()) {
            return $team->manager_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->hasPermission('delete_teams');
    }
}
