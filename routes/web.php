<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'renderLoginView'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    //
    Route::get('/dashboard', [DashboardController::class, 'renderView'])->name('dashboard');

    require __DIR__ . '/ticket.php';
    require __DIR__ . '/assets.php';
    require __DIR__ . '/access.php';
    require __DIR__ . '/admin-control.php';
    require __DIR__ . '/developments.php';
    require __DIR__ . '/settings.php';

    // notifications/12/mark-as-read
    Route::prefix('notifications')->group(function () {
        Route::patch('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::patch('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::delete('/read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    Route::redirect('settings', '/settings/appearance')->name('settings');
});

//* Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard');
});

