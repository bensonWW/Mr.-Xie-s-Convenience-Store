<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands (Database Maintenance)
|--------------------------------------------------------------------------
|
| These commands run automatically based on the defined schedule.
| Configure SCHEDULE_TIMEZONE in .env if needed.
|
*/

// Daily cleanup of soft-deleted records (runs at 3:00 AM)
Schedule::command('cleanup:soft-deletes')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->emailOutputOnFailure(env('ADMIN_EMAIL'));

// Weekly session cleanup (runs every Sunday at 4:00 AM)
// Note: Only needed if using database session driver
Schedule::command('session:gc')
    ->weeklyOn(0, '04:00')
    ->withoutOverlapping()
    ->runInBackground();
