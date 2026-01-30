<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\DevelopmentController;

Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {

    Route::prefix('developments')->group(function () {

        Route::get('/', [DevelopmentController::class, 'renderView'])->name('developments');
        Route::post('/', [DevelopmentController::class, 'store'])->name('developments.store');
        Route::patch('/position', [DevelopmentController::class, 'swapPositions'])->name('developments.updatePosition');
        Route::post('/{developmentRequest}/update', [DevelopmentController::class, 'update'])->name('developments.update');
        Route::patch('/{developmentRequest}/status', [DevelopmentController::class, 'updateStatus'])->name('developments.updateStatus');

        Route::patch('/{developmentRequest}/estimate', [DevelopmentController::class, 'estimate'])->name('developments.estimate');
        Route::post('/{developmentRequest}/approve-technical', [DevelopmentController::class, 'approveTechnical'])->name('developments.approveTechnical');
        Route::post('/{developmentRequest}/approve-strategic', [DevelopmentController::class, 'approveStrategic'])->name('developments.approveStrategic');


    });

});
