<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\AdminControlController;

Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::prefix('admin-control')->group(function () {

        Route::get('/', [AdminControlController::class, 'renderView'])->name('admin.control');

        Route::post('/', [AdminControlController::class, 'storeContract'])->name('admin.control.store.contract');
        Route::put('/{contract}', [AdminControlController::class, 'updateContract'])->name('admin.control.update.contract');



    });
});
