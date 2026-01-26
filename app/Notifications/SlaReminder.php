<?php

namespace App\Notifications;

use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlaReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The request instance.
     *
     * @var \App\Models\Request
     */
    protected $request;

    /**
     * The number of days remaining until the deadline.
     *
     * @var int
     */
    protected $daysRemaining;

    /**
     * The priority of the reminder (low, medium, high).
     *
     * @var string
     */
    protected $priority;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Request  $request
     * @param  int  $daysRemaining
     * @param  string  $priority
     * @return void
     */
    public function __construct(Request $request, int $daysRemaining, string $priority = 'medium')
    {
        $this->request = $request;
        $this->daysRemaining = $daysRemaining;
        $this->priority = $priority;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->getPriorityPrefix() . "RFQ #{$this->request->id} Deadline Approaching";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that RFQ #{$this->request->id}: {$this->request->title} is approaching its deadline.")
            ->line("There are {$this->daysRemaining} days remaining until the deadline on " . $this->request->deadline->format('F j, Y, g:i a'))
            ->line($this->getActionMessage())
            ->action('View RFQ Details', url("/rfq/{$this->request->id}"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'request_title' => $this->request->title,
            'days_remaining' => $this->daysRemaining,
            'priority' => $this->priority,
            'deadline' => $this->request->deadline->toIso8601String(),
        ];
    }

    /**
     * Get the priority prefix for the subject line.
     */
    private function getPriorityPrefix(): string
    {
        if ($this->priority === 'high') {
            return '🔴 URGENT: ';
        } elseif ($this->priority === 'medium') {
            return '🟠 REMINDER: ';
        }

        return 'REMINDER: ';
    }

    /**
     * Get the action message based on the priority.
     */
    private function getActionMessage(): string
    {
        if ($this->priority === 'high') {
            return 'Immediate action is required to ensure this RFQ is processed before the deadline.';
        } elseif ($this->priority === 'medium') {
            return 'Please ensure all necessary actions are taken before the deadline.';
        }

        return 'Please review the RFQ status and take any necessary actions.';
    }
}
