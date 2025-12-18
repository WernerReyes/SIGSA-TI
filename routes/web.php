<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/dashboard', function () {
    $user = Auth::user();
    return Inertia::render('Dashboard', [
        'user' => $user,
    ]);
})->middleware(['auth'])->name('dashboard');

Route::get('events', [App\Http\Controllers\EventsController::class, 'show'])->name('events.show');

// require __DIR__.'/settings.php';
