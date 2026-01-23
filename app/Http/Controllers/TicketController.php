<?php

namespace App\Http\Controllers;


use App\DTOs\Ticket\StoreTicketDto;
use App\DTOs\Ticket\TicketFiltersDto;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Inertia\Inertia;


class TicketController extends Controller
{
    //
    function renderView(Request $request, TicketService $ticketService, UserService $userService)
    {
        $filters = TicketFiltersDto::fromArray($request->all());

        $ticketId = $request->input('ticket_id');

        return Inertia::render('Tickets', [
            // 'departments' => $departmentsWithUsers,
            'filters' => $filters,
            'users' => Inertia::optional(fn() => $userService->getAllBasicInfo()),
            'tickets' => Inertia::once(fn() => $ticketService->getAllOrderedByPriority($filters)),
            'TIUsers' => Inertia::optional(fn() => $userService->getTIDepartmentUsers()),

            'historiesPaginated' => Inertia::optional(fn() => $ticketId ? $ticketService->getHistoriesPaginated(Ticket::find($ticketId)) : null),
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

    public function delete(Ticket $ticket, TicketService $ticketService)
    {
        try {
            $ticketService->deleteTicket($ticket);

            Inertia::flash([
                'success' => 'El ticket ha sido eliminado exitosamente.',
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

    function update(Ticket $ticket, StoreTicketRequest $request, TicketService $ticketService)
    {
        $validated = $request->validated();
        $dto = StoreTicketDto::fromArray($validated);
        try {
            $ticket = $ticketService->updateTicket($ticket, $dto);
            Inertia::flash([
                'ticket' => $ticket,
                'success' => 'El ticket ha sido actualizado exitosamente.',
                'error' => null,
                'timestamp' => now()->timestamp,
            ]);

        } catch (\Exception $e) {
       
            Inertia::flash([
                'ticket' => null,
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
            ['description' => $description, 'responsible' => $responsible] = $ticketService->assignTicket($ticket, $responsibleId);


            Inertia::flash([
                'responsible' => $responsible,
                'success' => $description,
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
            $description = $ticketService->changeTicketStatus($ticket, $newStatus);
            
            Inertia::flash([
                'success' => $description,
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

}
