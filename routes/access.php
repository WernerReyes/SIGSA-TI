<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\ServiceController;

Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::prefix('access')->group(function () {

        Route::get('/', [ServiceController::class, 'renderView'])->name('access');
        Route::post('/', [ServiceController::class, 'store'])->name('access.services.store');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('access.services.update');
        Route::delete('/{service}', [ServiceController::class, 'delete'])->name('access.services.delete');


    });
});
