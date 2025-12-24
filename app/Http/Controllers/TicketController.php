<?php

namespace App\Http\Controllers;


use App\DTOs\Ticket\StoreTicketDto;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Services\DepartmentService;
use App\Services\TicketService;
use Inertia\Inertia;


class TicketController extends Controller
{
    //
    function renderView(DepartmentService $departmentService, TicketService $ticketService)
    {
        $departmentsWithUsers = $departmentService->getTechDepartmentUsers();
        $ticketsOrderedByPriority = $ticketService->getAllOrderedByPriority();
        return Inertia::render('Tickets', [
            'departments' => $departmentsWithUsers,
            'tickets' => $ticketsOrderedByPriority,
        ]);
    }


    function store(StoreTicketRequest $request, TicketService $ticketService)
    {


        $validated = $request->validated();

        $dto = StoreTicketDto::fromArray($validated);


        try {
            $ticketService->storeTicket($dto);
            return redirect()->route('tickets')->with('success', 'El ticket ha sido creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ocurrió un error al crear el ticket. Por favor, inténtelo de nuevo.']);
        }

    }

}
