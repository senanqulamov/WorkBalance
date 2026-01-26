<?php

namespace Database\Factories;

use App\Models\WellbeingTool;
use Illuminate\Database\Eloquent\Factories\Factory;

class WellbeingToolFactory extends Factory
{
    public function definition(): array
    {
        $tools = [
            [
                'type' => 'breathing',
                'title' => '4-7-8 Breathing Exercise',
                'description' => 'A calming breathing technique: breathe in for 4, hold for 7, exhale for 8.',
                'duration_seconds' => 180,
                'content_data' => [
                    'steps' => [
                        'Find a comfortable seated position',
                        'Breathe in through your nose for 4 seconds',
                        'Hold your breath for 7 seconds',
                        'Exhale slowly through your mouth for 8 seconds',
                        'Repeat 4 times',
                    ],
                ],
            ],
            [
                'type' => 'grounding',
                'title' => '5-4-3-2-1 Grounding',
                'description' => 'Notice 5 things you see, 4 you hear, 3 you can touch, 2 you smell, 1 you taste.',
                'duration_seconds' => 300,
                'content_data' => [
                    'steps' => [
                        'Name 5 things you can see',
                        'Name 4 things you can hear',
                        'Name 3 things you can touch',
                        'Name 2 things you can smell',
                        'Name 1 thing you can taste',
                    ],
                ],
            ],
            [
                'type' => 'refocus',
                'title' => 'Quick Refocus Break',
                'description' => 'A 2-minute exercise to reset your focus and clarity.',
                'duration_seconds' => 120,
                'content_data' => [
                    'steps' => [
                        'Step away from your screen',
                        'Look at something far away for 20 seconds',
                        'Stretch your arms and shoulders',
                        'Take 3 deep breaths',
                        'Return to your task with fresh eyes',
                    ],
                ],
            ],
            [
                'type' => 'microrest',
                'title' => 'Micro-Rest Moment',
                'description' => 'A brief 60-second reset for your mind and body.',
                'duration_seconds' => 60,
                'content_data' => [
                    'steps' => [
                        'Close your eyes',
                        'Roll your shoulders back',
                        'Take 5 slow breaths',
                        'Smile gently',
                        'Return when ready',
                    ],
                ],
            ],
        ];

        $tool = fake()->randomElement($tools);

        return [
            'type' => $tool['type'],
            'title' => $tool['title'],
            'description' => $tool['description'],
            'duration_seconds' => $tool['duration_seconds'],
            'content_data' => $tool['content_data'],
            'is_active' => true,
        ];
    }
}
