<?php

use App\Http\Controllers\AssetController;
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
Route::post('/tickets', [TicketController::class, 'store'])->middleware(['auth'])->name('tickets.store');
Route::post('/tickets/{ticketId}/reassign', [TicketController::class, 'reassign'])->middleware(['auth'])->name('tickets.reassign');
Route::post('/tickets/{ticketId}/change-status', [TicketController::class, 'changeStatus'])->middleware(['auth'])->name('tickets.changeStatus');


Route::get('/assets', [AssetController::class, 'renderView'])->middleware(['auth'])->name('assets');
Route::post('/assets/types', [AssetController::class, 'registerType'])->middleware(['auth'])->name('assets.types.register');

require __DIR__ . '/settings.php';

//* Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard');
});

