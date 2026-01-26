<?php

namespace App\Notifications;

use App\Models\Request;
use App\Models\SupplierInvitation as SupplierInvitationModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupplierInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The request instance.
     *
     * @var \App\Models\Request
     */
    protected $request;

    /**
     * The supplier invitation instance.
     *
     * @var \App\Models\SupplierInvitation
     */
    protected $invitation;

    /**
     * The user who sent the invitation.
     *
     * @var \App\Models\User|null
     */
    protected $sender;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\SupplierInvitation  $invitation
     * @param  \App\Models\User|null  $sender
     * @return void
     */
    public function __construct(SupplierInvitationModel $invitation, ?User $sender = null)
    {
        $this->invitation = $invitation;
        $this->request = $invitation->request;
        $this->sender = $sender;
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
            ->subject("You've Been Invited to Submit a Quote")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been invited to submit a quote for RFQ #{$this->request->id}: {$this->request->title}.")
            ->line("Deadline for submission: " . $this->request->deadline->format('F j, Y, g:i a'))
            ->action('View RFQ Details', url("/supplier/rfq/{$this->request->id}"))
            ->line('Please log in to your account to view the full details and submit your quote.');

        if ($this->sender) {
            $message->line("This invitation was sent by {$this->sender->name}.");
        }

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
            'invitation_id' => $this->invitation->id,
            'request_id' => $this->request->id,
            'request_title' => $this->request->title,
            'deadline' => $this->request->deadline->toIso8601String(),
            'sender_id' => $this->sender?->id,
            'sender_name' => $this->sender?->name,
            'sent_at' => now()->toIso8601String(),
        ];
    }
}
