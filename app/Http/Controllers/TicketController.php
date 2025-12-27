<?php

namespace App\Http\Controllers;


use App\DTOs\Ticket\StoreTicketDto;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Services\DepartmentService;
use App\Services\TicketService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class TicketController extends Controller
{
    //
    function renderView(DepartmentService $departmentService, TicketService $ticketService, UserService $userService)
    {
        $departmentsWithUsers = $departmentService->getTechDepartmentUsers();
        $ticketsOrderedByPriority = $ticketService->getAllOrderedByPriority();
        $TIUsers = $userService->getTIDepartmentUsers();
        ds($ticketsOrderedByPriority->toArray());
        ds($TIUsers->toArray());
        return Inertia::render('Tickets', [
            'departments' => $departmentsWithUsers,
            'tickets' => $ticketsOrderedByPriority,
            'TIUsers' => $TIUsers,
        ]);
    }

    function storeAPI(StoreTicketRequest $request, TicketService $ticketService)
    {
        $validated = $request->validated();
        $dto = StoreTicketDto::fromArray($validated);
        try {
            $ticketService->storeTicket($dto);
            return response()->json(['message' => 'El ticket ha sido creado exitosamente.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error al crear el ticket. Por favor, inténtelo de nuevo.'], 500);
        }
    }


    function store(StoreTicketRequest $request, TicketService $ticketService)
    {

        $validated = $request->validated();
        $dto = StoreTicketDto::fromArray($validated);
        try {
            $ticketService->storeTicket($dto);
            // return redirect()->route('tickets')->with('success', 'El ticket ha sido creado exitosamente.');
            return back()->with('success', 'El ticket ha sido creado exitosamente.');
        } catch (\Exception $e) {
            ds($e->getMessage());
            return back()->withErrors(['error' => 'Ocurrió un error al crear el ticket. Por favor, inténtelo de nuevo.']);
        }

    }

    function reassign(int $ticketId, Request $request, TicketService $ticketService)
    {
        $newTechnicianId = $request->input('responsable_id');
        if (!$newTechnicianId) {
            return back()->withErrors(['error' => 'El nuevo técnico es obligatorio.']);
        }

        try {
            $action = $ticketService->reassignTicket($ticketId, $newTechnicianId);
            // return redirect()->route('tickets')->with('success', $action);
            return back()->with('success', $action);
        } catch (\Exception $e) {
            if ($e->getCode() === 500) {
                return back()->withErrors(['error' => 'Ocurrió un error al reasignar el ticket. Por favor, inténtelo de nuevo.']);
            }
        }
        return back()->withErrors(['error' => $e->getMessage()]);
    }

    function changeStatus(int $ticketId, Request $request, TicketService $ticketService)
    {
        $newStatus = $request->input('status');
        if (!$newStatus) {
            return back()->withErrors(['error' => 'El nuevo estado es obligatorio.']);
        }

        try {
            $action = $ticketService->changeTicketStatus($ticketId, $newStatus);
            return back()->with('success', $action);
        } catch (\Exception $e) {
            if ($e->getCode() === 500) {
                return back()->withErrors(['error' => 'Ocurrió un error al cambiar el estado del ticket. Por favor, inténtelo de nuevo.']);
            }
        }
        return back()->withErrors(['error' => $e->getMessage()]);
    }

}
