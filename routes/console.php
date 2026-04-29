<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tally scheduled sync — runs hourly when sync mode = 'scheduled'
Schedule::command('tally:sync')->hourly()->when(function () {
    return \App\Models\SystemSetting::get('tally_sync_mode') === 'scheduled';
});

// Retry failed Tally syncs every 2 hours
Schedule::command('tally:sync --retry')->everyTwoHours()->when(function () {
    return \App\Models\SystemSetting::get('tally_sync_mode') === 'scheduled';
});
