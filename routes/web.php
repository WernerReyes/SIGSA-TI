<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/tickets', function () {
    return Inertia::render('Tickets');
})->middleware(['auth'])->name('tickets');

Route::get('events', [App\Http\Controllers\EventsController::class, 'show'])->name('events.show');

// require __DIR__.'/settings.php';
