<?php

namespace Database\Seeders;

use App\Models\EmotionalCheckIn;
use App\Models\Organization;
use App\Models\Team;
use App\Models\TherapeuticSession;
use App\Models\User;
use App\Services\TeamMetricsAggregationService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the WorkBalance database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting WorkBalance database seeding...');

        // STEP 1: Roles and permissions
        $this->command->info('📋 Step 1: Creating roles and permissions...');
        $this->call(RolesAndPermissionsSeeder::class);

        // STEP 2: Therapeutic paths and categories
        $this->command->info('🌿 Step 2: Creating therapeutic paths...');
        $this->call(WorkBalanceSeeder::class);

        // STEP 3: Get or create organization (WorkBalanceSeeder may have created it)
        $this->command->info('🏢 Step 3: Setting up organization...');
        $org = Organization::firstOrCreate(
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

        // STEP 4: Create teams
        $this->command->info('👥 Step 4: Creating teams...');
        $teams = [
            ['name' => 'Engineering Team', 'manager_name' => 'Alice Johnson', 'manager_email' => 'alice@techcorp.example'],
            ['name' => 'Design Team', 'manager_name' => 'Bob Smith', 'manager_email' => 'bob@techcorp.example'],
            ['name' => 'Product Team', 'manager_name' => 'Carol White', 'manager_email' => 'carol@techcorp.example'],
            ['name' => 'Marketing Team', 'manager_name' => 'David Brown', 'manager_email' => 'david@techcorp.example'],
        ];

        $createdTeams = [];
        foreach ($teams as $teamData) {
            $manager = User::factory()->create([
                'name' => $teamData['manager_name'],
                'email' => $teamData['manager_email'],
                'password' => bcrypt('password'),
            ]);
            $manager->roles()->attach(\App\Models\Role::where('name', 'manager')->first());

            $team = Team::create([
                'organization_id' => $org->id,
                'name' => $teamData['name'],
                'description' => "The {$teamData['name']} at TechCorp",
                'manager_id' => $manager->id,
                'is_active' => true,
            ]);

            $createdTeams[] = ['team' => $team, 'manager' => $manager];
        }

        // STEP 5: Create employees and assign to teams
        $this->command->info('🧑‍💼 Step 5: Creating employees...');
        $employeeRole = \App\Models\Role::where('name', 'employee')->first();

        foreach ($createdTeams as $teamInfo) {
            // 6-8 employees per team for realistic cohort sizes
            $employeeCount = rand(6, 8);

            for ($i = 0; $i < $employeeCount; $i++) {
                $employee = User::factory()->create([
                    'password' => bcrypt('password'),
                ]);
                $employee->roles()->attach($employeeRole);
                $employee->teams()->attach($teamInfo['team']);
            }
        }

        // STEP 6: Create sample check-ins for the past week
        $this->command->info('💚 Step 6: Creating emotional check-ins...');
        $allEmployees = User::whereHas('roles', fn($q) => $q->where('name', 'employee'))->get();

        foreach ($allEmployees as $employee) {
            // Create check-ins for past 7 days
            for ($day = 0; $day < 7; $day++) {
                if (rand(0, 100) > 20) { // 80% participation rate
                    EmotionalCheckIn::factory()->create([
                        'employee_id' => $employee->id,
                        'checked_in_at' => now()->subDays($day)->setHour(rand(8, 18)),
                    ]);
                }
            }
        }

        // STEP 7: Create sample therapeutic sessions
        $this->command->info('🌱 Step 7: Creating therapeutic sessions...');
        $therapeuticPaths = \App\Models\TherapeuticPath::all();

        foreach ($allEmployees->random(min(15, $allEmployees->count())) as $employee) {
            TherapeuticSession::factory()
                ->completed()
                ->create([
                    'employee_id' => $employee->id,
                    'therapeutic_path_id' => $therapeuticPaths->random()->id,
                    'started_at' => now()->subDays(rand(1, 7)),
                ]);
        }

        // STEP 8: Aggregate team metrics
        $this->command->info('📊 Step 8: Aggregating team metrics...');
        $aggregationService = app(TeamMetricsAggregationService::class);

        foreach (Team::all() as $team) {
            for ($day = 0; $day < 7; $day++) {
                $date = now()->subDays($day);
                try {
                    $aggregationService->aggregateTeamMetrics($team, $date);
                } catch (\Exception $e) {
                    // Skip if cohort too small
                }
            }
        }

        // STEP 9: Feature flags
        $this->command->info('🚩 Step 9: Setting feature flags...');
        $this->call(FeatureFlagsSeeder::class);

        $this->command->info('✅ WorkBalance database seeded successfully!');
        $this->command->line('');
        $this->command->info('📝 Login credentials:');
        $this->command->line('   Admin: admin@workbalance.local / password');
        $this->command->line('   Manager: alice@techcorp.example / password');
        $this->command->line('   Employee: (any created user) / password');
    }
}
