<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Complete WorkBalance + HumanOps Schema
     */
    public function up(): void
    {
        $this->comment('🚀 Starting WorkBalance + HumanOps database migration...');

        // =============================================
        // AUTH TABLES (Laravel Foundation)
        // =============================================
        $this->createUsersTable();

        // =============================================
        // ACCESS CONTROL TABLES (Roles & Permissions)
        // =============================================
        $this->createRolesTable();
        $this->createPermissionsTable();
        $this->createRoleUserTable();
        $this->createPermissionRoleTable();

        // =============================================
        // SYSTEM TABLES (Logging & Features)
        // =============================================
        $this->createLogsTable();
        $this->createFeatureFlagsTable();

        // =============================================
        // CORE TABLES (Foundation)
        // =============================================
        $this->createOrganizationsTable();
        $this->createDepartmentsTable();

        // =============================================
        // WORKBALANCE TABLES (Employee-Facing)
        // =============================================
        $this->createEmployeeProfilesTable();
        $this->createDailyCheckInsTable();
        $this->createCheckInReflectionsTable();
        $this->createPersonalTrendsCacheTable();
        $this->createWellbeingToolsTable();
        $this->createToolUsageLogsTable();
        $this->createEmployeePrivacySettingsTable();

        // =============================================
        // BRIDGE TABLES (Controlled Connection)
        // =============================================
        $this->createAggregationExportsTable();
        $this->createPrivacyAuditLogTable();

        // =============================================
        // HUMANOPS TABLES (Employer-Facing)
        // =============================================
        $this->createAggregatedWellbeingSignalsTable();
        $this->createBurnoutRiskSignalsTable();
        $this->createFinancialStressSignalsTable();
        $this->createRelationshipHealthSignalsTable();
        $this->createTrendSnapshotsTable();
        $this->createRecommendationsTable();
        $this->createHumanopsViewsLogTable();

        $this->comment('✅ All tables created successfully!');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->comment('🗑️ Dropping all WorkBalance + HumanOps tables...');

        Schema::dropIfExists('humanops_views_log');
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('trend_snapshots');
        Schema::dropIfExists('relationship_health_signals');
        Schema::dropIfExists('financial_stress_signals');
        Schema::dropIfExists('burnout_risk_signals');
        Schema::dropIfExists('aggregated_wellbeing_signals');
        Schema::dropIfExists('privacy_audit_log');
        Schema::dropIfExists('aggregation_exports');
        Schema::dropIfExists('employee_privacy_settings');
        Schema::dropIfExists('tool_usage_logs');
        Schema::dropIfExists('wellbeing_tools');
        Schema::dropIfExists('personal_trends_cache');
        Schema::dropIfExists('check_in_reflections');
        Schema::dropIfExists('daily_check_ins');
        Schema::dropIfExists('employee_profiles');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('logs');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        $this->comment('✅ All tables dropped!');
    }

    // =============================================
    // TABLE CREATION METHODS
    // =============================================

    protected function createUsersTable(): void
    {
        $this->comment('👥 Creating users table...');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('employee'); // employee, employer, admin
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->index(['role', 'is_active']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    protected function createOrganizationsTable(): void
    {
        $this->comment('📊 Creating organizations table...');

        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('size_range')->nullable()->comment('e.g., 1-50, 51-200');
            $table->timestamps();
        });
    }

    protected function createDepartmentsTable(): void
    {
        $this->comment('🏢 Creating departments table...');

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('manager_id')->nullable(); // No foreign key yet
            $table->timestamps();

            $table->index(['organization_id', 'is_active']);
        });
    }

    protected function createEmployeeProfilesTable(): void
    {
        $this->comment('👤 Creating employee_profiles table...');

        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role_title')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('locale')->default('en');
            $table->timestamps();

            $table->index(['department_id', 'user_id']);
        });
    }

    protected function createDailyCheckInsTable(): void
    {
        $this->comment('✅ Creating daily_check_ins table (WORKBALANCE - PRIVATE)...');

        Schema::create('daily_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('stress_level', ['low', 'medium', 'high'])->nullable();
            $table->integer('stress_value')->nullable()->comment('1-5 numeric value');
            $table->enum('energy_level', ['low', 'medium', 'high'])->nullable();
            $table->integer('energy_value')->nullable()->comment('1-5 numeric value');
            $table->string('mood_state', 50)->nullable();
            $table->text('optional_note')->nullable();
            $table->date('check_in_date')->index();
            $table->timestamps();

            $table->unique(['user_id', 'check_in_date']);
            $table->index(['user_id', 'created_at']);
        });
    }

    protected function createCheckInReflectionsTable(): void
    {
        $this->comment('📝 Creating check_in_reflections table...');

        Schema::create('check_in_reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('reflection_text');
            $table->foreignId('related_check_in_id')->nullable()->constrained('daily_check_ins')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    protected function createPersonalTrendsCacheTable(): void
    {
        $this->comment('📈 Creating personal_trends_cache table...');

        Schema::create('personal_trends_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('period', ['weekly', 'monthly']);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('avg_stress', 3, 2)->nullable();
            $table->decimal('avg_energy', 3, 2)->nullable();
            $table->decimal('mood_stability', 3, 2)->nullable();
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['user_id', 'period', 'period_start']);
        });
    }

    protected function createWellbeingToolsTable(): void
    {
        $this->comment('🛠️ Creating wellbeing_tools table...');

        Schema::create('wellbeing_tools', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['breathing', 'grounding', 'refocus', 'microrest']);
            $table->string('title');
            $table->text('description');
            $table->integer('duration_seconds');
            $table->json('content_data')->nullable()->comment('Instructions, steps, etc.');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    protected function createToolUsageLogsTable(): void
    {
        $this->comment('📊 Creating tool_usage_logs table...');

        Schema::create('tool_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tool_id')->constrained('wellbeing_tools')->cascadeOnDelete();
            $table->integer('duration_seconds')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamp('used_at');
            $table->timestamps();

            $table->index(['user_id', 'used_at']);
        });
    }

    protected function createEmployeePrivacySettingsTable(): void
    {
        $this->comment('🔒 Creating employee_privacy_settings table...');

        Schema::create('employee_privacy_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('allow_aggregation')->default(true);
            $table->boolean('allow_trend_use')->default(true);
            $table->timestamp('last_updated');
            $table->timestamps();
        });
    }

    protected function createAggregationExportsTable(): void
    {
        $this->comment('🌉 Creating aggregation_exports table (THE BRIDGE)...');

        Schema::create('aggregation_exports', function (Blueprint $table) {
            $table->id();
            $table->enum('period', ['weekly', 'monthly']);
            $table->date('period_start');
            $table->date('period_end');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('exported_at');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->integer('records_count')->default(0);
            $table->integer('min_group_size_met')->nullable()->comment('Actual group size');
            $table->timestamps();

            $table->index(['period', 'department_id', 'period_start']);
        });
    }

    protected function createPrivacyAuditLogTable(): void
    {
        $this->comment('📋 Creating privacy_audit_log table...');

        Schema::create('privacy_audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_id')->nullable()->constrained('aggregation_exports')->nullOnDelete();
            $table->json('rules_applied')->comment('List of privacy rules checked');
            $table->integer('min_group_size')->comment('Minimum group size enforced');
            $table->integer('actual_group_size')->nullable();
            $table->integer('delay_hours')->comment('Time delay applied');
            $table->boolean('passed')->default(true);
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    protected function createAggregatedWellbeingSignalsTable(): void
    {
        $this->comment('📊 Creating aggregated_wellbeing_signals table (HUMANOPS SOURCE)...');

        Schema::create('aggregated_wellbeing_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('period', ['weekly', 'monthly']);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('avg_stress', 3, 2)->nullable();
            $table->decimal('avg_energy', 3, 2)->nullable();
            $table->decimal('mood_index', 3, 2)->nullable();
            $table->decimal('data_confidence', 3, 2)->comment('0-1 confidence score');
            $table->integer('participant_count')->comment('Group size for transparency');
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index(['department_id', 'period', 'period_start'], 'agg_signals_dept_period_idx');
        });
    }

    protected function createBurnoutRiskSignalsTable(): void
    {
        $this->comment('⚠️ Creating burnout_risk_signals table...');

        Schema::create('burnout_risk_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('risk_level', ['low', 'moderate', 'elevated', 'high']);
            $table->enum('trend_direction', ['improving', 'stable', 'declining']);
            $table->text('description')->nullable();
            $table->decimal('signal_strength', 3, 2)->nullable();
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('mitigated_at')->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index(['department_id', 'calculated_at']);
            $table->index('detected_at');
            $table->index('mitigated_at');
        });
    }

    protected function createFinancialStressSignalsTable(): void
    {
        $this->comment('💰 Creating financial_stress_signals table...');

        Schema::create('financial_stress_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('stress_level', ['low', 'moderate', 'high']);
            $table->enum('trend_direction', ['improving', 'stable', 'worsening']);
            $table->text('description')->nullable();
            $table->decimal('signal_strength', 3, 2)->nullable();
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('mitigated_at')->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index(['department_id', 'calculated_at']);
            $table->index('detected_at');
            $table->index('mitigated_at');
        });
    }

    protected function createRelationshipHealthSignalsTable(): void
    {
        $this->comment('🤝 Creating relationship_health_signals table...');

        Schema::create('relationship_health_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('strain_level', ['low', 'moderate', 'high']);
            $table->decimal('volatility', 3, 2)->nullable()->comment('Mood instability');
            $table->decimal('signal_strength', 3, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('mitigated_at')->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index(['department_id', 'calculated_at']);
            $table->index('detected_at');
            $table->index('mitigated_at');
        });
    }

    protected function createTrendSnapshotsTable(): void
    {
        $this->comment('📸 Creating trend_snapshots table...');

        Schema::create('trend_snapshots', function (Blueprint $table) {
            $table->id();
            $table->enum('scope', ['organization', 'department']);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('metric')->comment('stress, energy, mood, etc.');
            $table->decimal('value', 5, 2);
            $table->enum('period', ['weekly', 'monthly']);
            $table->date('period_start');
            $table->timestamps();

            $table->index(['scope', 'department_id', 'metric', 'period_start'], 'trends_scope_metric_idx');
        });
    }

    protected function createRecommendationsTable(): void
    {
        $this->comment('💡 Creating recommendations table...');

        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->enum('scope', ['organization', 'department']);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->comment('workload, communication, leadership');
            $table->string('title');
            $table->text('text');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamp('generated_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['scope', 'department_id', 'generated_at']);
        });
    }

    protected function createHumanopsViewsLogTable(): void
    {
        $this->comment('🔍 Creating humanops_views_log table...');

        Schema::create('humanops_views_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('section')->comment('overview, departments, trends');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('viewed_at');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'viewed_at']);
            $table->index('section');
        });
    }

    protected function createRolesTable(): void
    {
        $this->comment('👥 Creating roles table...');

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false)->comment('System roles cannot be deleted');
            $table->timestamps();
        });
    }

    protected function createPermissionsTable(): void
    {
        $this->comment('🔐 Creating permissions table...');

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('group')->nullable()->comment('Permission grouping');
            $table->timestamps();

            $table->index('group');
        });
    }

    protected function createRoleUserTable(): void
    {
        $this->comment('🔗 Creating role_user pivot table...');

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id']);
            $table->index('user_id');
            $table->index('role_id');
        });
    }

    protected function createPermissionRoleTable(): void
    {
        $this->comment('🔗 Creating permission_role pivot table...');

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['permission_id', 'role_id']);
            $table->index('permission_id');
            $table->index('role_id');
        });
    }

    protected function createLogsTable(): void
    {
        $this->comment('📋 Creating logs table (Activity Audit)...');

        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->comment('auth, user, role, system, etc.');
            $table->string('action')->comment('login, create, update, delete, etc.');
            $table->string('model')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('message')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at');

            $table->index(['user_id', 'created_at']);
            $table->index(['model', 'model_id']);
            $table->index('type');
            $table->index('created_at');
        });
    }

    protected function createFeatureFlagsTable(): void
    {
        $this->comment('🚩 Creating feature_flags table...');

        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->json('config')->nullable()->comment('Additional flag configuration');
            $table->timestamps();

            $table->index('is_enabled');
        });
    }

    /**
     * Output a comment during migration
     */
    protected function comment(string $message): void
    {
        echo "\n" . $message;
    }
};
