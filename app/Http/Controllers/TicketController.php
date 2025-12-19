<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use UserService;

class TicketController extends Controller
{
    //
    function renderView(UserService $userService)
    {
        $technicians = $userService->getTechnicians();
        return inertia()::render('Tickets', [
            'technicians' => $technicians,
        ]);
    }

}
