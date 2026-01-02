<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\AssetController;
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


    Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::get('/assets', [AssetController::class, 'renderView'])->name('assets');
    Route::post('/assets', [AssetController::class, 'storeAsset'])->name('assets.store');
    Route::put('/assets', [AssetController::class, 'updateAsset'])->name('assets.update');
    Route::post('/assets/assign', [AssetController::class, 'assignAsset'])->name('assets.assign');
    Route::get('/assets/generate-laptop-assignment-doc/{assetId}', [AssetController::class, 'generateLaptopAssignmentDocument'])->name('assets.generateLaptopAssignmentDocument');
    Route::get('/assets/generate-phone-assignment-doc/{assetId}', [AssetController::class, 'generateCellphoneAssignmentDocument'])->name('assets.generatePhoneAssignmentDocument');


    Route::post('/assets/types', [AssetController::class, 'registerType'])->name('assets.types.register');
    Route::delete('/assets/types', [AssetController::class, 'deleteType'])->name('assets.types.delete');
    });
});


// Route::get('/tickets', [TicketController::class, 'renderView'])->name('tickets');
// Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
// Route::post('/tickets/{ticketId}/reassign', [TicketController::class, 'reassign'])->name('tickets.reassign');
// Route::post('/tickets/{ticketId}/change-status', [TicketController::class, 'changeStatus'])->name('tickets.changeStatus');


// Route::get('/assets', [AssetController::class, 'renderView'])->name('assets');
// Route::post('/assets', [AssetController::class, 'storeAsset'])->name('assets.store');
// Route::put('/assets', [AssetController::class, 'updateAsset'])->name('assets.update');
// Route::post('/assets/assign', [AssetController::class, 'assignAsset'])->name('assets.assign');
// Route::post('/assets/types', [AssetController::class, 'registerType'])->name('assets.types.register');
// Route::delete('/assets/types', [AssetController::class, 'deleteType'])->name('assets.types.delete');

require __DIR__ . '/settings.php';

//* Fallback Route
Route::fallback(function () {
    return redirect()->route('dashboard');
});

