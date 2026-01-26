<?php

namespace App\Notifications;

use App\Models\Quote;
use App\Models\Request;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteReceived extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The request instance.
     *
     * @var \App\Models\Request
     */
    protected $request;

    /**
     * The quote instance.
     *
     * @var \App\Models\Quote
     */
    protected $quote;

    /**
     * The supplier user instance.
     *
     * @var \App\Models\User
     */
    protected $supplier;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Quote  $quote
     * @return void
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
        $this->request = $quote->request;
        $this->supplier = $quote->supplier;
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
        return (new MailMessage)
            ->subject("New Quote Received for RFQ #{$this->request->id}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new quote has been submitted for RFQ #{$this->request->id}: {$this->request->title}.")
            ->line("Supplier: {$this->supplier->name}")
            ->line("Total Quote Amount: $" . number_format($this->quote->total_price, 2))
            ->line("Submitted on: " . $this->quote->created_at->format('F j, Y, g:i a'))
            ->action('View Quote Details', url("/rfq/{$this->request->id}/quotes/{$this->quote->id}"))
            ->line('You can compare all received quotes in the RFQ details page.');
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
            'quote_id' => $this->quote->id,
            'supplier_id' => $this->supplier->id,
            'supplier_name' => $this->supplier->name,
            'total_price' => $this->quote->total_price,
            'submitted_at' => $this->quote->created_at->toIso8601String(),
        ];
    }
}
