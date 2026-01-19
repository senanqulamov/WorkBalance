<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Models\Request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected Request $request;
    protected Quote $quote;
    protected string $oldStatus;
    protected string $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Quote $quote, string $oldStatus, string $newStatus)
    {
        $this->quote = $quote;
        $this->request = $quote->request;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
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
            ->subject("Quote Status Update for RFQ #{$this->request->id}")
            ->greeting("Hello {$notifiable->name},");

        if ($this->newStatus === 'accepted') {
            $message->line("Congratulations! Your quote for RFQ #{$this->request->id}: {$this->request->title} has been accepted.")
                ->line("Quote Amount: $" . number_format($this->quote->total_price ?? $this->quote->total_amount ?? 0, 2))
                ->line("The buyer will contact you soon with next steps.")
                ->action('View Quote Details', url("/supplier/quotes/{$this->quote->id}/edit"));
        } elseif ($this->newStatus === 'rejected') {
            $message->line("Your quote for RFQ #{$this->request->id}: {$this->request->title} has been declined.")
                ->line("Quote Amount: $" . number_format($this->quote->total_price ?? $this->quote->total_amount ?? 0, 2))
                ->line("Thank you for your participation. We hope to work with you on future opportunities.")
                ->action('View Quote Details', url("/supplier/quotes/{$this->quote->id}/edit"));
        } else {
            $message->line("The status of your quote for RFQ #{$this->request->id}: {$this->request->title} has been updated.")
                ->line("New Status: " . ucfirst($this->newStatus))
                ->action('View Quote Details', url("/supplier/quotes/{$this->quote->id}/edit"));
        }

        return $message->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'quote_id' => $this->quote->id,
            'request_id' => $this->request->id,
            'request_title' => $this->request->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'total_price' => $this->quote->total_price ?? $this->quote->total_amount ?? 0,
        ];
    }
}
