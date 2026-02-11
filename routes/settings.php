<?php


use App\Enums\Department\Allowed;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;


Route::prefix('settings')->group(function () {

    Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {
        Route::get('/sla', [SettingController::class, 'renderSlaPolicies'])->name('sla.edit');
        Route::put('/sla', [SettingController::class, 'updateSlaPolicy'])->name('sla.update');
    });
    Route::get('/appearance', [SettingController::class, 'renderAppearanceSettings'])->name('appearance.edit');
});