<?php

namespace App\Jobs;

use App\Events\SlaReminderDue;
use App\Models\Request;
use App\Models\WorkflowEvent;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckRfqDeadlines implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Use explicit day thresholds and database-side date filtering to avoid timezone/partial-day issues
        $thresholds = [7, 3, 1];

        foreach ($thresholds as $days) {
            $date = now()->addDays($days)->toDateString();

            $rfqs = Request::with(['buyer', 'supplierInvitations.supplier'])
                ->whereIn('status', ['draft', 'open'])
                ->whereDate('deadline', $date)
                ->get();

            foreach ($rfqs as $rfq) {
                try {
                    // Deduplicate: skip if a workflow event for this rfq with same days_remaining exists today
                    $already = WorkflowEvent::where('eventable_type', get_class($rfq))
                        ->where('eventable_id', $rfq->id)
                        ->where('event_type', 'sla_reminder')
                        ->where('metadata->days_remaining', $days)
                        ->whereDate('occurred_at', now()->toDateString())
                        ->exists();

                    if ($already) {
                        continue;
                    }

                    // Determine priority and fire event
                    $priority = $this->determinePriority($days);

                    event(new SlaReminderDue($rfq, $days, $priority));
                } catch (\Throwable $e) {
                    // Log and continue with other RFQs
                    Log::error('Error checking RFQ deadline for reminder', [
                        'rfq_id' => $rfq->id,
                        'days' => $days,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Determine the priority based on days remaining.
     *
     * @param int $daysRemaining
     * @return string
     */
    private function determinePriority(int $daysRemaining): string
    {
        if ($daysRemaining <= 1) {
            return 'high';
        } elseif ($daysRemaining <= 3) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Determine if a reminder should be sent based on days remaining.
     *
     * @param int $daysRemaining
     * @return bool
     */
    private function shouldSendReminder(int $daysRemaining): bool
    {
        // Send reminders at specific thresholds: 7 days, 3 days, 1 day
        return in_array($daysRemaining, [7, 3, 1]);
    }
}
