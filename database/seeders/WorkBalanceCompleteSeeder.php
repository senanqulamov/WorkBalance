<?php

namespace Database\Seeders;

use App\Models\DailyCheckIn;
use App\Models\Department;
use App\Models\EmployeePrivacySetting;
use App\Models\EmployeeProfile;
use App\Models\Organization;
use App\Models\User;
use App\Models\WellBeingToolUsage;
use App\Models\WellbeingTool;
use App\Services\AggregationService;
use App\Services\PersonalInsightsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkBalanceCompleteSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌟 Starting WorkBalance + HumanOps Complete Seeding...');
        $this->command->newLine();

        // Step 0: Seed Roles and Permissions FIRST
        $this->command->info('🔐 Seeding roles and permissions...');
        $this->call(RolesAndPermissionsSeeder::class);
        $this->command->newLine();

        // Step 0.5: Seed Feature Flags
        $this->command->info('🚩 Seeding feature flags...');
        $this->call(FeatureFlagsSeeder::class);
        $this->command->newLine();

        // Step 1: Create Organization
        $this->command->info('📊 Creating organization...');
        $organization = Organization::create([
            'name' => 'TechCorp Solutions',
            'industry' => 'Technology',
            'size_range' => '201-500',
        ]);
        $this->command->info("✓ Organization created: {$organization->name}");
        $this->command->newLine();

        // Step 2: Create Departments
        $this->command->info('🏢 Creating departments...');
        $departments = [
            ['name' => 'Engineering', 'code' => 'ENG001'],
            ['name' => 'Product', 'code' => 'PRD001'],
            ['name' => 'Marketing', 'code' => 'MKT001'],
            ['name' => 'Sales', 'code' => 'SAL001'],
            ['name' => 'Human Resources', 'code' => 'HR001'],
        ];

        $createdDepartments = collect();
        foreach ($departments as $dept) {
            $department = Department::firstOrCreate(
                ['code' => $dept['code']],
                [
                    'organization_id' => $organization->id,
                    'name' => $dept['name'],
                    'is_active' => true,
                ]
            );
            $createdDepartments->push($department);
            $this->command->info("  ✓ {$department->name} ({$department->code})");
        }
        $this->command->newLine();

        // Step 3: Create Admin User
        $this->command->info('👤 Creating admin user...');
        $admin = User::firstOrCreate(
            ['email' => 'admin@techcorp.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole && !$admin->roles->contains($adminRole->id)) {
            $admin->roles()->attach($adminRole);
        }

        $this->command->info("✓ Admin: {$admin->email}");
        $this->command->newLine();

        // Step 4: Create Employees per Department (10-15 per dept)
        $this->command->info('👥 Creating employees and profiles...');
        $totalEmployees = 0;

        foreach ($createdDepartments as $department) {
            $employeeCount = fake()->numberBetween(10, 15);

            for ($i = 1; $i <= $employeeCount; $i++) {
                $user = User::create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                EmployeeProfile::create([
                    'user_id' => $user->id,
                    'department_id' => $department->id,
                    'role_title' => fake()->randomElement([
                        'Senior Engineer', 'Software Engineer', 'Product Manager',
                        'Designer', 'Marketing Specialist', 'Sales Rep', 'HR Coordinator'
                    ]),
                    'timezone' => 'UTC',
                    'locale' => 'en',
                ]);

                // Assign employee role
                $employeeRole = \App\Models\Role::where('name', 'employee')->first();
                if ($employeeRole && !$user->roles->contains($employeeRole->id)) {
                    $user->roles()->attach($employeeRole);
                }

                // Privacy settings (most opt-in)
                EmployeePrivacySetting::create([
                    'user_id' => $user->id,
                    'allow_aggregation' => fake()->boolean(90), // 90% opt-in
                    'allow_trend_use' => true,
                    'last_updated' => now(),
                ]);

                $totalEmployees++;
            }

            $this->command->info("  ✓ {$department->name}: {$employeeCount} employees");
        }
        $this->command->info("✓ Total employees created: {$totalEmployees}");
        $this->command->info("✓ Roles assigned to all users");
        $this->command->newLine();

        // Step 5: Create Check-Ins (last 30 days, 48+ hours ago)
        $this->command->info('✅ Creating daily check-ins (respecting 48-hour delay)...');
        $checkInCount = 0;

        $employees = User::where('role', 'employee')->get();

        foreach ($employees as $employee) {
            // Random check-in frequency (70-90% days)
            $daysToCheckIn = fake()->numberBetween(21, 27); // 21-27 days out of 30

            for ($day = 30; $day >= 3; $day--) { // Only up to 3 days ago (48+ hours)
                if (fake()->boolean(($daysToCheckIn / 30) * 100)) {
                    DailyCheckIn::create([
                        'user_id' => $employee->id,
                        'stress_level' => $stressLevel = fake()->randomElement(['low', 'medium', 'high']),
                        'stress_value' => match($stressLevel) {
                            'low' => fake()->numberBetween(1, 2),
                            'medium' => fake()->numberBetween(3, 3),
                            'high' => fake()->numberBetween(4, 5),
                        },
                        'energy_level' => $energyLevel = fake()->randomElement(['low', 'medium', 'high']),
                        'energy_value' => match($energyLevel) {
                            'low' => fake()->numberBetween(1, 2),
                            'medium' => fake()->numberBetween(3, 3),
                            'high' => fake()->numberBetween(4, 5),
                        },
                        'mood_state' => fake()->randomElement(['great', 'good', 'okay', 'low', 'struggling']),
                        'optional_note' => fake()->optional(0.2)->sentence(),
                        'check_in_date' => now()->subDays($day)->toDateString(),
                    ]);
                    $checkInCount++;
                }
            }
        }
        $this->command->info("✓ Created {$checkInCount} check-ins");
        $this->command->newLine();

        // Step 6: Create Well-being Tools
        $this->command->info('🛠️ Creating well-being tools...');
        $tools = [
            [
                'type' => 'breathing',
                'title' => '4-7-8 Breathing',
                'description' => 'Breathe in for 4, hold for 7, exhale for 8.',
                'duration_seconds' => 180,
            ],
            [
                'type' => 'grounding',
                'title' => '5-4-3-2-1 Grounding',
                'description' => 'Notice 5 things you see, 4 you hear, 3 you touch, 2 you smell, 1 you taste.',
                'duration_seconds' => 300,
            ],
            [
                'type' => 'refocus',
                'title' => 'Quick Refocus',
                'description' => 'A 2-minute mental reset.',
                'duration_seconds' => 120,
            ],
            [
                'type' => 'microrest',
                'title' => 'Micro-Rest',
                'description' => 'A 60-second body and mind reset.',
                'duration_seconds' => 60,
            ],
        ];

        foreach ($tools as $tool) {
            WellbeingTool::create($tool + ['is_active' => true]);
            $this->command->info("  ✓ {$tool['title']}");
        }
        $this->command->newLine();

        // Step 6.5: Create tool usage logs (for WorkBalance dashboard + insights)
        $this->command->info('🧘 Creating tool usage logs...');
        $toolModels = WellbeingTool::query()->where('is_active', true)->get();
        $usageCount = 0;

        foreach ($employees as $employee) {
            // Roughly 40% of employees use tools a few times per month
            if (!fake()->boolean(40) || $toolModels->isEmpty()) {
                continue;
            }

            $sessions = fake()->numberBetween(1, 6);
            for ($i = 0; $i < $sessions; $i++) {
                $tool = $toolModels->random();

                WellBeingToolUsage::create([
                    'user_id' => $employee->id,
                    'tool_id' => $tool->id,
                    'duration_seconds' => fake()->randomElement([$tool->duration_seconds, null]),
                    'completed' => fake()->boolean(80),
                    // Respect the same 48h delay vibe: keep usage at least 2 days old.
                    'used_at' => now()->subDays(fake()->numberBetween(3, 20))->subMinutes(fake()->numberBetween(1, 500)),
                ]);

                $usageCount++;
            }
        }

        $this->command->info("✓ Created {$usageCount} tool usage logs");
        $this->command->newLine();

        // Step 7: Run Aggregation Service
        $this->command->info('🌉 Running aggregation service...');
        try {
            $aggregationService = new AggregationService();
            $aggregationService->runWeeklyAggregation();
            $this->command->info('✓ Aggregation completed successfully');
        } catch (\Exception $e) {
            $this->command->warn("⚠ Aggregation warning: {$e->getMessage()}");
            $this->command->info('  (This may be normal if groups are too small)');
        }
        $this->command->newLine();

        // Step 7.5: Generate personal insights (employee-only)
        $this->command->info('💡 Generating personal insights...');
        try {
            (new PersonalInsightsService())->generateInsightsForAllEmployees();
            $this->command->info('✓ Personal insights generated');
        } catch (\Exception $e) {
            $this->command->warn("⚠ Personal insights warning: {$e->getMessage()}");
        }
        $this->command->newLine();

        // Summary
        $this->command->info('========================================');
        $this->command->info('   ✅ SEEDING COMPLETE');
        $this->command->info('========================================');
        $this->command->newLine();
        $this->command->info('📊 Summary:');
        $this->command->info("  • Organizations: 1");
        $this->command->info("  • Departments: {$createdDepartments->count()}");
        $this->command->info("  • Employees: {$totalEmployees}");
        $this->command->info("  • Check-ins: {$checkInCount}");
        $this->command->info("  • Well-being Tools: " . WellbeingTool::count());
        $this->command->newLine();
        $this->command->info('🔐 Login Credentials:');
        $this->command->info('  Admin: admin@techcorp.com / password');
        $this->command->info('  Any Employee: [employee-email] / password');
        $this->command->newLine();
    }
}
