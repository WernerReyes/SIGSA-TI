<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\AdminControlController;


Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::prefix('admin-control')->group(function () {
        Route::get('/', [AdminControlController::class, 'renderView'])->name('admin.control');

        Route::post('/', [AdminControlController::class, 'storeContract'])->name('admin.control.store.contract');
        Route::put('/{contract}', [AdminControlController::class, 'updateContract'])->name('admin.control.update.contract');
        Route::post('/{contract}/renew', [AdminControlController::class, 'renew'])->name('admin.control.renew.contract');
        Route::post('/{contract}/cancel', [AdminControlController::class, 'cancel'])->name('admin.control.cancel.contract');
        Route::delete('/{contract}', [AdminControlController::class, 'destroy'])->name('admin.control.delete.contract');
        Route::post('/infrastructure-events', [AdminControlController::class, 'storeInfrastructureEvent'])->name('admin.control.store.infrastructure.event');
        Route::put('/infrastructure-events/{event}', [AdminControlController::class, 'updateInfrastructureEvent'])->name('admin.control.update.infrastructure.event');
        Route::delete('/infrastructure-events/{event}', [AdminControlController::class, 'destroyInfrastructureEvent'])->name('admin.control.delete.infrastructure.event');


    });
});
