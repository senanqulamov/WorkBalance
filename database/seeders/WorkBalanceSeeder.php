<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\PathCategory;
use App\Models\Team;
use App\Models\TherapeuticPath;
use App\Models\PathStep;
use Illuminate\Database\Seeder;

class WorkBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏢 Creating organizations...');

        // Create or find organization
        $org1 = Organization::firstOrCreate(
            ['slug' => 'techcorp-solutions'],
            [
                'name' => 'TechCorp Solutions',
                'industry' => 'Technology',
                'size' => '51-200',
                'primary_contact_email' => 'hr@techcorp.example',
                'settings' => [
                    'minimum_cohort_size' => 5,
                    'enable_burnout_alerts' => true,
                    'enable_stress_tracking' => true,
                ],
            ]
        );

        $this->command->info('📚 Creating therapeutic path categories...');

        // Create therapeutic path categories
        $stressCategory = PathCategory::firstOrCreate(
            ['slug' => 'stress-management'],
            [
                'name' => 'Stress Management',
                'description' => 'Paths for managing workplace stress and pressure',
                'icon' => 'shield',
                'color' => 'blue',
                'sort_order' => 1,
            ]
        );

        $conflictCategory = PathCategory::firstOrCreate(
            ['slug' => 'conflict-resolution'],
            [
                'name' => 'Conflict Resolution',
                'description' => 'Support for navigating workplace conflicts',
                'icon' => 'heart',
                'color' => 'purple',
                'sort_order' => 2,
            ]
        );

        $motivationCategory = PathCategory::firstOrCreate(
            ['slug' => 'motivation-energy'],
            [
                'name' => 'Motivation & Energy',
                'description' => 'Rebuild motivation and recover energy',
                'icon' => 'sun',
                'color' => 'yellow',
                'sort_order' => 3,
            ]
        );

        $this->command->info('🛤️ Creating therapeutic paths...');

        // Create therapeutic paths with steps
        $deadlinePath = TherapeuticPath::firstOrCreate(
            ['slug' => 'deadline-pressure'],
            [
                'path_category_id' => $stressCategory->id,
                'name' => 'Deadline Pressure Management',
                'description' => 'A gentle path to navigate urgent deadlines without overwhelming stress.',
                'situation_trigger' => 'I feel overwhelmed by an approaching deadline',
                'estimated_duration_minutes' => 10,
                'is_active' => true,
            ]
        );

        // Only create steps if path was just created
        if ($deadlinePath->wasRecentlyCreated || $deadlinePath->steps()->count() === 0) {
            PathStep::create([
                'therapeutic_path_id' => $deadlinePath->id,
                'step_order' => 1,
                'step_type' => 'validation',
                'title' => 'Your feelings make sense',
                'validation_text' => "It's completely understandable to feel pressure when facing a deadline. Your nervous system is responding to real time constraints. This doesn't mean you can't handle it—it means you're human.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $deadlinePath->id,
                'step_order' => 2,
                'step_type' => 'regulation',
                'title' => 'Calm your nervous system',
                'regulation_exercise' => "Take three slow breaths. On each exhale, let your shoulders drop. Place one hand on your chest and feel it rise and fall. You're safe right now in this moment.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $deadlinePath->id,
                'step_order' => 3,
                'step_type' => 'insight',
                'title' => 'Separate urgency from ability',
                'insight_text' => "The deadline creates urgency, but urgency doesn't define your capability. You've completed work before. The pressure is external—your skills remain steady.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $deadlinePath->id,
                'step_order' => 4,
                'step_type' => 'action',
                'title' => 'One micro-step',
                'micro_action' => "Choose the smallest possible next task—something you can complete in 5 minutes. Write it down. Do just that one thing. Progress beats perfection.",
                'prompt' => 'What is your 5-minute task?',
            ]);
        }

        // Burnout prevention path
        $burnoutPath = TherapeuticPath::firstOrCreate(
            ['slug' => 'emotional-exhaustion'],
            [
                'path_category_id' => $stressCategory->id,
                'name' => 'Emotional Exhaustion Support',
                'description' => 'Recognize and respond to emotional burnout signals.',
                'situation_trigger' => 'I feel emotionally drained and exhausted',
                'estimated_duration_minutes' => 12,
                'is_active' => true,
            ]
        );

        if ($burnoutPath->wasRecentlyCreated || $burnoutPath->steps()->count() === 0) {
            PathStep::create([
                'therapeutic_path_id' => $burnoutPath->id,
                'step_order' => 1,
                'step_type' => 'validation',
                'title' => 'Exhaustion is a signal, not a weakness',
                'validation_text' => "Emotional exhaustion means you've been giving a lot—to tasks, to people, to responsibilities. Your body and mind are asking for care. This is wisdom, not failure.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $burnoutPath->id,
                'step_order' => 2,
                'step_type' => 'regulation',
                'title' => 'Permission to pause',
                'regulation_exercise' => "Close your eyes for 30 seconds. Notice the weight of your body in the chair. You don't have to solve anything right now. Just breathe and rest here.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $burnoutPath->id,
                'step_order' => 3,
                'step_type' => 'insight',
                'title' => 'Depletion needs boundaries, not more effort',
                'insight_text' => "When you're running on empty, pushing harder won't refill you. Recovery requires saying 'not right now' to something—even something small.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $burnoutPath->id,
                'step_order' => 4,
                'step_type' => 'action',
                'title' => 'Name one boundary',
                'micro_action' => "What's one thing you can defer, delegate, or decline today? Not forever—just today. Write it down. Practice the phrase: 'I need to pause on this for now.'",
                'prompt' => 'What will you defer or decline today?',
            ]);
        }

        // Conflict resolution path
        $conflictPath = TherapeuticPath::firstOrCreate(
            ['slug' => 'workplace-conflict'],
            [
                'path_category_id' => $conflictCategory->id,
                'name' => 'Navigating Workplace Conflict',
                'description' => 'Process and respond to interpersonal tension with clarity.',
                'situation_trigger' => 'I had a difficult interaction with a colleague',
                'estimated_duration_minutes' => 15,
                'is_active' => true,
            ]
        );

        if ($conflictPath->wasRecentlyCreated || $conflictPath->steps()->count() === 0) {
            PathStep::create([
                'therapeutic_path_id' => $conflictPath->id,
                'step_order' => 1,
                'step_type' => 'validation',
                'title' => 'Conflict stirs up big feelings',
                'validation_text' => "It makes sense that conflict feels uncomfortable—it activates our threat response. Feeling upset, defensive, or hurt doesn't mean you handled it wrong. It means you care.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $conflictPath->id,
                'step_order' => 2,
                'step_type' => 'regulation',
                'title' => 'Settle before responding',
                'regulation_exercise' => "Ground yourself: name 3 things you can see, 2 you can hear, 1 you can touch. Your nervous system needs to calm before your mind can problem-solve.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $conflictPath->id,
                'step_order' => 3,
                'step_type' => 'insight',
                'title' => 'Separate impact from intent',
                'insight_text' => "Their behavior had an impact on you, even if that wasn't their intent. You can acknowledge both: 'This affected me' and 'They may not have meant harm.' Both can be true.",
            ]);

            PathStep::create([
                'therapeutic_path_id' => $conflictPath->id,
                'step_order' => 4,
                'step_type' => 'action',
                'title' => 'Decide: respond, release, or request support',
                'micro_action' => "You have options. Will you address it directly with them? Let it go for now? Ask a manager or HR for support? There's no 'right' choice—only what feels manageable for you.",
                'prompt' => 'What feels like the right next step for you?',
            ]);
        }

        $this->command->info('✅ WorkBalance therapeutic paths seeded successfully!');        $this->command->info('✅ WorkBalance therapeutic paths seeded successfully');
    }
}
