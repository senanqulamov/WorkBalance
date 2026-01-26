<?php

use App\Http\Controllers\LocaleController;
use App\Livewire\HumanOps\Dashboard as HumanOpsDashboard;
use App\Livewire\HumanOps\Teams\Index as HumanOpsTeamsIndex;
use App\Livewire\HumanOps\Teams\Show as HumanOpsTeamsShow;
use App\Livewire\HumanOps\Insights\Index as HumanOpsInsightsIndex;
use App\Livewire\HumanOps\Risk\Matrix as HumanOpsRiskMatrix;
use App\Livewire\HumanOps\Recommendations\Index as HumanOpsRecommendationsIndex;
use App\Livewire\WorkBalance\Dashboard as WorkBalanceDashboard;
use App\Livewire\WorkBalance\CheckIn;
use App\Livewire\WorkBalance\SituationSelector;
use App\Livewire\WorkBalance\TherapeuticFlow;
use App\Livewire\WorkBalance\ReflectionSummary;
use App\Livewire\WorkBalance\Progress;
use App\Livewire\WorkBalance\Settings;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');
Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

Route::middleware(['auth'])->group(function () {
    // Employer-facing HumanOps Intelligence (admin panel replacement)
    Route::get('/dashboard', HumanOpsDashboard::class)->name('dashboard');

    Route::prefix('humanops')->name('humanops.')->group(function () {
        Route::get('/dashboard', HumanOpsDashboard::class)->name('dashboard');
        Route::get('/teams', HumanOpsTeamsIndex::class)->name('teams.index');
        Route::get('/teams/{team}', HumanOpsTeamsShow::class)->name('teams.show');
        Route::get('/insights', HumanOpsInsightsIndex::class)->name('insights.index');
        Route::get('/risk/matrix', HumanOpsRiskMatrix::class)->name('risk.matrix');
        Route::get('/recommendations', HumanOpsRecommendationsIndex::class)->name('recommendations.index');
    });

    // Employee-facing WorkBalance app
    Route::prefix('workbalance')->name('workbalance.')->group(function () {
        Route::get('/dashboard', WorkBalanceDashboard::class)->name('dashboard');
        Route::get('/check-in', CheckIn::class)->name('check-in');
        Route::get('/situations', SituationSelector::class)->name('situations');
        Route::get('/flow', TherapeuticFlow::class)->name('flow');
        Route::get('/reflection', ReflectionSummary::class)->name('reflection');
        Route::get('/progress', Progress::class)->name('progress');
        Route::get('/settings', Settings::class)->name('settings');
    });
});

require __DIR__.'/auth.php';
