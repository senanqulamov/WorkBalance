<?php

namespace App\Notifications;

use App\Models\TeamMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifies managers when their team's burnout risk reaches elevated or high.
 * Tone: Supportive, calm, action-oriented.
 */
class BurnoutRiskAlert extends Notification
{
    use Queueable;

    public function __construct(
        public TeamMetric $metric
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $teamName = $this->metric->team->name;
        $riskLevel = $this->metric->burnout_risk_level;

        return (new MailMessage)
            ->subject("Team Wellbeing Alert: {$teamName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your team **{$teamName}** is showing signs of elevated pressure.")
            ->line("**Burnout Risk Level:** " . ucfirst($riskLevel))
            ->line("This is an aggregated signal from the team as a whole—not tied to any individual.")
            ->line("**Suggested Actions:**")
            ->line("• Review workload distribution and upcoming deadlines")
            ->line("• Consider offering flexible scheduling or recovery time")
            ->line("• Check in with the team about pacing and support needs")
            ->action('View Team Insights', route('humanops.teams.show', $this->metric->team_id))
            ->line("Remember: Early support prevents burnout. Your team benefits from proactive care.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'burnout_risk_alert',
            'team_id' => $this->metric->team_id,
            'team_name' => $this->metric->team->name,
            'risk_level' => $this->metric->burnout_risk_level,
            'metric_date' => $this->metric->metric_date->toDateString(),
        ];
    }
}
