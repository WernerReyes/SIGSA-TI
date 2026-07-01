<?php

use App\Http\Controllers\Api\DevelopmentRequestApiController;
use App\Http\Controllers\Api\TicketApiController;

Route::middleware('access.api')->prefix('tickets')->group(function () {
    Route::get('/', [TicketApiController::class, 'index']);
    Route::post('/', [TicketApiController::class, 'store']);
    Route::post('/{id}/close', [TicketApiController::class, 'close']);
    Route::get('/{id}', [TicketApiController::class, 'show']);
    Route::put('/{id}', [TicketApiController::class, 'update']);
    Route::delete('/{id}', [TicketApiController::class, 'destroy']);
});

Route::middleware('access.api')->prefix('development-requests')->group(function () {
    Route::post('/', [DevelopmentRequestApiController::class, 'store']);
});
