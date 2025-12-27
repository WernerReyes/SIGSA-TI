<?php

use App\Http\Controllers\TicketController;

Route::post('/tickets', [TicketController::class, 'storeAPI']);