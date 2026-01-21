<?php

namespace App\Http\Controllers;


use App\DTOs\Ticket\StoreTicketDto;
use App\DTOs\Ticket\TicketFiltersDto;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Models\Ticket;
use App\Services\DepartmentService;
use App\Services\TicketService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;


class TicketController extends Controller
{
    //
    function renderView(Request $request, DepartmentService $departmentService, TicketService $ticketService, UserService $userService)
    {
        $filters = TicketFiltersDto::fromArray($request->all());

        return Inertia::render('Tickets', [
            // 'departments' => $departmentsWithUsers,
            'filters' => $filters,
            'tickets' => Inertia::once(fn() => $ticketService->getAllOrderedByPriority($filters)),
            'TIUsers' => Inertia::optional(fn() => $userService->getTIDepartmentUsers()),
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

            Inertia::flash([

                'success' => 'El ticket ha sido creado exitosamente.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {

            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }

    function assign(Ticket $ticket, Request $request, TicketService $ticketService)
    {
        $responsibleId = $request->input('responsible_id');
        if (!$responsibleId) {
            Inertia::flash([
                'success' => null,
                'error' => 'El nuevo responsable es obligatorio.',
                'timestamp' => now()->timestamp,
            ]);
            return back();
        }

        try {
            ['action' => $action, 'responsible' => $responsible] = $ticketService->assignTicket($ticket, $responsibleId);


            Inertia::flash([
                'responsible' => $responsible,
                'success' => $action,
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            Inertia::flash([
                'responsible' => null,
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);

        }

        return back();
    }

    function changeStatus(Ticket $ticket, Request $request, TicketService $ticketService)
    {
        $newStatus = $request->input('status');
        if (!$newStatus) {
            Inertia::flash([
                'success' => null,
                'error' => 'El nuevo estado es obligatorio.',
                'timestamp' => now()->timestamp,
            ]);
            return back();
        }

        try {
            $action = $ticketService->changeTicketStatus($ticket->id, $newStatus);
            // return back()->with('success', $action);

            Inertia::flash([
                'success' => $action,
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
            // if ($e->getCode() === 500) {
            //     return back()->withErrors(['error' => 'Ocurrió un error al cambiar el estado del ticket. Por favor, inténtelo de nuevo.']);
            // }
            Inertia::flash([
                'success' => null,
                'error' => $e->getMessage(),
                'timestamp' => now()->timestamp,
            ]);
        }

        return back();
    }

}
