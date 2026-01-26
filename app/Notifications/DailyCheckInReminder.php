<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Gentle reminder for employees to complete their daily check-in.
 * Tone: Supportive, never pushy. This is an invitation, not a requirement.
 */
class DailyCheckInReminder extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("A gentle check-in invitation")
            ->greeting("Hi {$notifiable->name},")
            ->line("Taking a moment for yourself today?")
            ->line("Your WorkBalance check-in is ready whenever you are. No pressure—just a quiet space to pause and reflect.")
            ->action('Check In', route('workbalance.check-in'))
            ->line("Your check-ins are private and never shared with your employer.")
            ->line("Take care of yourself. 💙");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'check_in_reminder',
            'message' => 'Your daily check-in is ready',
        ];
    }
}
