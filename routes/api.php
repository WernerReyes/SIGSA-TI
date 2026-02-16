<?php

use App\Http\Controllers\TicketController;

Route::post('/tickets', [TicketController::class, 'storeAPI']);
Route::put('/tickets/{id}', [TicketController::class, 'updateAPI']);