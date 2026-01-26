<?php

namespace App\Policies;

use App\Models\EmotionalCheckIn;
use App\Models\User;

/**
 * Policy for EmotionalCheckIn access control.
 *
 * PRIVACY RULE: Only the employee who created the check-in can access it.
 * Employers NEVER have access to individual check-ins.
 */
class EmotionalCheckInPolicy
{
    public function viewAny(User $user): bool
    {
        // Users can only view their own check-ins
        return true;
    }

    public function view(User $user, EmotionalCheckIn $checkIn): bool
    {
        // PRIVACY: Only the employee themselves can view their check-in
        // Even admins cannot override this
        return $checkIn->employee_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_check_ins');
    }

    public function update(User $user, EmotionalCheckIn $checkIn): bool
    {
        // Only the owner can update their check-in
        return $checkIn->employee_id === $user->id;
    }

    public function delete(User $user, EmotionalCheckIn $checkIn): bool
    {
        // Only the owner can delete their check-in
        return $checkIn->employee_id === $user->id;
    }
}
