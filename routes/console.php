<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Daily Database Backup & Supabase Cloud Sync
Schedule::command('db:backup-supabase')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backup.log'));

// Auto-expire stale quiz attempts that exceed their duration
Schedule::command('quizzes:expire-attempts')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

