<?php

use App\Http\Controllers\LocaleController;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Logs\Index as LogsIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use App\Livewire\User\Profile;
use App\Livewire\Users\Index;
use App\Livewire\Users\Show as UsersShow;
use App\Livewire\Privacy\Index as PrivacyIndex;
use App\Livewire\Privacy\Users\Show as PrivacyUserShow;
use App\Livewire\Privacy\Roles\Show as PrivacyRoleShow;
use Illuminate\Support\Facades\Route;
use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Livewire\WorkBalance\DailyCheckin\Index as WorkBalanceDailyCheckinIndex;
use App\Livewire\WorkBalance\Trends\Index as WorkBalanceTrendsIndex;
use App\Livewire\WorkBalance\Tools\Index as WorkBalanceToolsIndex;
use App\Livewire\WorkBalance\Insights\Index as WorkBalanceInsightsIndex;
use App\Livewire\WorkBalance\Dashboard\Index as WorkBalanceDashboardIndex;

Route::view('/', 'welcome')->name('welcome');

Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

Route::middleware(['auth'])->prefix('fluxa')->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard')->middleware('can:view_dashboard');

    // Shared Routes (Users, Privacy, Logs, Settings, Notifications, Search)
    Route::get('/users', Index::class)->name('users.index')->middleware('can:view_users');
    Route::get('/users/{user}', UsersShow::class)->name('users.show')->middleware('can:view_users');
    Route::get('/user/profile', Profile::class)->name('user.profile');

    // Logs
    Route::get('/logs', LogsIndex::class)->name('logs.index')->middleware('can:view_logs');

    // Settings
    Route::get('/settings', SettingsIndex::class)->name('settings.index')->middleware('can:view_settings');
    Route::get('/settings/flags', \App\Livewire\Settings\FeatureFlags::class)->name('settings.flags')->middleware('can:manage_feature_flags');

    // Privacy & Roles Management
    Route::get('/privacy', PrivacyIndex::class)->name('privacy.index')->middleware('can:manage_roles');
    Route::get('/privacy/users/{user}', PrivacyUserShow::class)->name('privacy.users.show')->middleware('can:manage_roles');
    Route::get('/privacy/roles/{role}', PrivacyRoleShow::class)->name('privacy.roles.show')->middleware('can:manage_roles');

    // Notifications
    Route::get('/notifications', NotificationsIndex::class)->name('notifications.index')->middleware('can:view_notifications');
});

// HumanOps Intelligence (Employer-facing organizational well-being system)
Route::middleware(['auth'])->prefix('humanops')->name('humanops.')->group(function () {
    // Overview (formerly Dashboard)
    Route::get('/overview', \App\Livewire\HumanOps\Overview\Index::class)->name('overview')->middleware('can:view_dashboard');

    // Departments - aggregated department-level insights
    Route::get('/departments', \App\Livewire\HumanOps\Departments\Index::class)->name('departments')->middleware('can:view_dashboard');

    // Risk Signals - detected organizational risks
    Route::get('/risk-signals', \App\Livewire\HumanOps\RiskSignals\Index::class)->name('risk-signals')->middleware('can:view_dashboard');

    // Recommendations - human-readable suggested actions
    Route::get('/recommendations', \App\Livewire\HumanOps\Recommendations\Index::class)->name('recommendations')->middleware('can:view_dashboard');

    // Trends - time-based well-being changes
    Route::get('/trends', \App\Livewire\HumanOps\Trends\Index::class)->name('trends')->middleware('can:view_dashboard');

    // Privacy - transparency and trust indicators
    Route::get('/privacy', \App\Livewire\HumanOps\Privacy\Index::class)->name('privacy')->middleware('can:view_dashboard');

    // WellBeing - aggregated wellbeing snapshot (org + departments)
    Route::get('/wellbeing', \App\Livewire\HumanOps\WellBeing\Index::class)
        ->name('wellbeing')
        ->middleware('can:view_dashboard');

    // Prevention - risk signals + recommendations hub
    Route::get('/prevention', \App\Livewire\HumanOps\Prevention\Index::class)
        ->name('prevention')
        ->middleware('can:view_dashboard');

    // Legacy redirect (dashboard -> overview)
    Route::get('/dashboard', function() {
        return redirect()->route('humanops.overview');
    })->name('dashboard');
});

// WorkBalance (Employee-facing wellness application)
Route::middleware(['auth'])->prefix('workbalance')->name('workbalance.')->group(function () {
    // Dashboard (home)
    Route::get('/', WorkBalanceDashboardIndex::class)->name('home');
    Route::get('/dashboard', WorkBalanceDashboardIndex::class)->name('dashboard');

    // Daily Check-in
    Route::get('/daily-checkin', WorkBalanceDailyCheckinIndex::class)->name('daily-checkin');

    // Personal Trends
    Route::get('/trends', WorkBalanceTrendsIndex::class)->name('trends');

    // Well-being Tools
    Route::get('/tools', WorkBalanceToolsIndex::class)->name('tools');

    // Insights
    Route::get('/insights', WorkBalanceInsightsIndex::class)->name('insights');
});

require __DIR__.'/auth.php';
