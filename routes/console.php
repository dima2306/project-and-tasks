<?php

use App\Console\Commands\SendDailyDigestEmailCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendDailyDigestEmailCommand::class)
    ->dailyAt('09:00');
