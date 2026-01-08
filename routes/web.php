<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/login', [AuthController::class, 'renderLoginView'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    //
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');


    Route::get('/tickets', [TicketController::class, 'renderView'])->name('tickets');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/{ticketId}/reassign', [TicketController::class, 'reassign'])->name('tickets.reassign');
    Route::post('/tickets/{ticketId}/change-status', [TicketController::class, 'changeStatus'])->name('tickets.changeStatus');

    require __DIR__ . '/assets.php';
});


require __DIR__ . '/settings.php';

//* Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard');
});

