<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/login', [AuthController::class, 'renderLoginView'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');


Route::get('/tickets', [TicketController::class, 'renderView'])->middleware(['auth'])->name('tickets');


require __DIR__ . '/settings.php';

//* Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard');
});

