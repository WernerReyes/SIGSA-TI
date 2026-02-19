<?php


use App\Enums\Department\Allowed;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;


Route::prefix('settings')->group(function () {

// use App\Http\Controllers\AssetTypeController;
    Route::prefix('asset-types')->group(function () {
        Route::get('/', [AssetTypeController::class, 'renderTypes'])->name('asset-types');
        Route::post('/', [AssetTypeController::class, 'store'])->name('asset-types.store');
        Route::put('/{type}', [AssetTypeController::class, 'update'])->name('asset-types.update');
        Route::delete('/{type}', [AssetTypeController::class, 'destroy'])->name('asset-types.delete');
    });

    Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {
        Route::get('/sla', [SettingController::class, 'renderSlaPolicies'])->name('sla.edit');
        Route::put('/sla', [SettingController::class, 'updateSlaPolicy'])->name('sla.update');
    });
    Route::get('/appearance', [SettingController::class, 'renderAppearanceSettings'])->name('appearance.edit');
});