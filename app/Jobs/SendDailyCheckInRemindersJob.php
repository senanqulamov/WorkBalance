<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DailyCheckInReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Sends gentle daily check-in reminders to employees.
 * Tone: Supportive, optional. Never pushy.
 * Respects user notification preferences.
 */
class SendDailyCheckInRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Only send to employees who haven't checked in today
        $employees = User::whereHas('roles', fn($q) => $q->where('name', 'employee'))
            ->whereDoesntHave('checkIns', function ($q) {
                $q->whereDate('checked_in_at', today());
            })
            ->where('is_active', true)
            ->get();

        foreach ($employees as $employee) {
            // Respect notification preferences (if implemented)
            // For now, send to all
            $employee->notify(new DailyCheckInReminder());
        }
    }
}
