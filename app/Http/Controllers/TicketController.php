<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    //
    function renderView(UserService $userService)
    {
        $technicians = $userService->getTechnicians();
        return Inertia::render('Tickets', [
            'technicians' => $technicians,
        ]);
    }

}
