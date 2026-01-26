<?php

namespace App\Policies;

use App\Models\TherapeuticSession;
use App\Models\User;

/**
 * Policy for TherapeuticSession access control.
 *
 * PRIVACY RULE: Only the employee who created the session can access it.
 * Employers NEVER have access to individual sessions or reflections.
 */
class TherapeuticSessionPolicy
{
    public function viewAny(User $user): bool
    {
        // Users can only view their own sessions
        return true;
    }

    public function view(User $user, TherapeuticSession $session): bool
    {
        // PRIVACY: Only the employee themselves can view their session
        return $session->employee_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('start_therapeutic_sessions');
    }

    public function update(User $user, TherapeuticSession $session): bool
    {
        // Only the owner can update their session
        return $session->employee_id === $user->id;
    }

    public function delete(User $user, TherapeuticSession $session): bool
    {
        // Only the owner can delete their session
        return $session->employee_id === $user->id;
    }
}
