<?php

namespace App\Notifications;

use App\Models\TeamMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies managers when team stress trends shift (rising, volatile).
 * Tone: Informative, supportive, non-alarmist.
 */
class StressTrendUpdate extends Notification
{
    use Queueable;

    public function __construct(
        public TeamMetric $metric,
        public string $previousTrend
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $teamName = $this->metric->team->name;
        $newTrend = $this->metric->stress_trend;

        $trendMessage = match($newTrend) {
            'cooling' => "Stress levels are easing—good signs of recovery.",
            'steady' => "Stress levels remain stable.",
            'rising' => "Stress levels are increasing. Consider checking in with the team.",
            'volatile' => "Stress patterns show fluctuation. Team may need support with pacing.",
            default => "Stress trend has changed.",
        };

        return (new MailMessage)
            ->subject("Stress Trend Update: {$teamName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your team **{$teamName}** has shown a shift in stress patterns.")
            ->line("**Previous Trend:** " . ucfirst($this->previousTrend))
            ->line("**Current Trend:** " . ucfirst($newTrend))
            ->line($trendMessage)
            ->action('View Team Dashboard', route('humanops.teams.show', $this->metric->team_id))
            ->line("These insights are aggregated across the team to protect individual privacy.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'stress_trend_update',
            'team_id' => $this->metric->team_id,
            'team_name' => $this->metric->team->name,
            'previous_trend' => $this->previousTrend,
            'new_trend' => $this->metric->stress_trend,
            'metric_date' => $this->metric->metric_date->toDateString(),
        ];
    }
}
