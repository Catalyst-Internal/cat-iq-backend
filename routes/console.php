<?php

use App\Jobs\RefreshGitHubCacheJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('github:sync-org')->everySixHours()->withoutOverlapping();

Schedule::job(new RefreshGitHubCacheJob)->everyFiveMinutes()->withoutOverlapping();
Schedule::job(new RefreshGitHubCacheJob('activity'))->everyMinute()->withoutOverlapping();
