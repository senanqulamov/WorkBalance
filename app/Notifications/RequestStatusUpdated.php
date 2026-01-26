<?php

namespace App\Notifications;

use App\Enums\RequestStatus;
use App\Models\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The request instance.
     *
     * @var \App\Models\Request
     */
    protected $request;

    /**
     * The previous status.
     *
     * @var \App\Enums\RequestStatus|null
     */
    protected $oldStatus;

    /**
     * The new status.
     *
     * @var \App\Enums\RequestStatus
     */
    protected $newStatus;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Request  $request
     * @param  \App\Enums\RequestStatus|null  $oldStatus
     * @param  \App\Enums\RequestStatus  $newStatus
     * @return void
     */
    public function __construct(Request $request, ?RequestStatus $oldStatus, RequestStatus $newStatus)
    {
        $this->request = $request;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $message = (new MailMessage)
            ->subject("RFQ Status Updated: {$this->request->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The status of RFQ #{$this->request->id} ({$this->request->title}) has been updated.");

        if ($this->oldStatus) {
            $message->line("Previous status: {$this->oldStatus->label()}");
        }

        $message->line("New status: {$this->newStatus->label()}")
            ->action('View RFQ', url("/rfq/{$this->request->id}"))
            ->line('Thank you for using our application!');

        return $message;
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
            'old_status' => $this->oldStatus?->value,
            'old_status_label' => $this->oldStatus?->label(),
            'new_status' => $this->newStatus->value,
            'new_status_label' => $this->newStatus->label(),
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
