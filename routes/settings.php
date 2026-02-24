<?php


use App\Enums\Department\Allowed;
use App\Http\Controllers\AssetModelController;
use App\Http\Controllers\AssetTypeController;
use App\Http\Controllers\BrandController;
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

    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'renderBrands'])->name('brands');
        Route::post('/', [BrandController::class, 'store'])->name('brands.store');
        Route::put('/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/{brand}', [BrandController::class, 'destroy'])->name('brands.delete');
    });

    Route::prefix('models')->group(function () {
        Route::get('/', [AssetModelController::class, 'renderModels'])->name('models');
        Route::post('/', [AssetModelController::class, 'store'])->name('models.store');
        Route::put('/{model}', [AssetModelController::class, 'update'])->name('models.update');
        Route::delete('/{model}', [AssetModelController::class, 'destroy'])->name('models.delete');
    });

    Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {
        Route::get('/sla', [SettingController::class, 'renderSlaPolicies'])->name('sla.edit');
        Route::put('/sla', [SettingController::class, 'updateSlaPolicy'])->name('sla.update');
    });
    Route::get('/appearance', [SettingController::class, 'renderAppearanceSettings'])->name('appearance.edit');
});