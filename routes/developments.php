<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\DevelopmentController;

Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::prefix('developments')->group(function () {

        Route::get('/', [DevelopmentController::class, 'renderView'])->name('developments');
        Route::post('/', [DevelopmentController::class, 'store'])->name('developments.store');
    });
    
});
