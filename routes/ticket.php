<?php

use App\Enums\Department\Allowed;
use App\Http\Controllers\TicketController;

Route::prefix('tickets')->group(function () {
    Route::get('/', [TicketController::class, 'renderView'])->name('tickets');
    Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
    Route::put('/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::delete('/{ticket}', [TicketController::class, 'delete'])->name('tickets.delete');

    Route::middleware('department:' . Allowed::SYSTEM_TI->value)->group(function () {
        Route::post('/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
        Route::post('/{ticket}/change-status', [TicketController::class, 'changeStatus'])->name('tickets.changeStatus');
    });

});
