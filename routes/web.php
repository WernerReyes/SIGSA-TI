<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
     Log::info('dashboard accessed');
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    ds('dashboard');
    Log::info('dashboard accessed');
    return Inertia::render('Dashboard');
});

Route::get('events', [App\Http\Controllers\EventsController::class, 'show'])->name('events.show');

require __DIR__.'/settings.php';
