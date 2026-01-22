<?php
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
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

    // Route::prefix('users')->group(function () {
    //     Route::get('/basic-info', [UserController::class, 'renderBasicInfo'])->name('users.basicInfo');
    // });

    // Route::prefix('departments')->group(function () {
    //     Route::get('/basic-info', [DepartmentController::class, 'renderBasicInfo'])->name('departments.basicInfo');
    // });

    // Route::prefix('asset-types')->group(function () {
    //     Route::get('/', [AssetTypeController::class, 'renderTypes'])->name('assetTypes');
    // });

    require __DIR__ . '/ticket.php';
    require __DIR__ . '/assets.php';
    require __DIR__ . '/settings.php';
});



//* Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard');
});

