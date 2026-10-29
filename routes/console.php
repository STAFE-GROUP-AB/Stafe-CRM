<?php

use App\Jobs\RecalculateLeadScoresJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('health-scores:recalculate')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/health-scores.log'));

// Recalculate stale lead scores daily
Schedule::job(new RecalculateLeadScoresJob(forceRecalculate: false, staleHoursThreshold: 24))
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Artisan command for manual recalculation
Artisan::command('lead-scores:recalculate {--force : Force recalculate all scores} {--contact= : Specific contact ID}', function () {
    $force = $this->option('force');
    $contactId = $this->option('contact');

    $this->info('Starting lead score recalculation...');

    RecalculateLeadScoresJob::dispatchSync($force, 24, $contactId ? (int) $contactId : null);

    $this->info('Lead score recalculation completed!');
})->purpose('Recalculate lead scores for all contacts or a specific contact');

// Backup Schedule
Schedule::command("backup:clean")->daily()->at("01:00");
Schedule::command("backup:run")->daily()->at("01:30");
