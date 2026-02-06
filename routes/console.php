<?php

use App\Console\Commands\CheckContractBilling;
use App\Console\Commands\CheckContractExpiration;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(CheckContractExpiration::class)
    ->dailyAt('8:30')
    ->timezone(env('APP_TIMEZONE', 'America/Lima'))  
    ->onSuccess(function () {
        Log::info('Contract expiration check completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Failed to check contract expiration.');
    });

Schedule::command(CheckContractBilling::class)
    ->dailyAt('8:30')
    ->timezone(env('APP_TIMEZONE', 'America/Lima'))
    ->onSuccess(function () {
        Log::info('Contract billing check completed successfully.');
    })
    ->onFailure(function () {
        Log::error('Failed to check contract billing.');
    });