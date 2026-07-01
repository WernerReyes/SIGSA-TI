<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Ticket\StoreTicketDto;
use App\DTOs\Ticket\TicketFiltersDto;
use App\DTOs\Ticket\TicketHistoryFiltersDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class TicketApiController extends Controller
{
    public function index(Request $request, TicketService $ticketService)
    {
        $filters = TicketFiltersDto::fromArray($request->all());
        $tickets = $ticketService->getAllOrderedByPriority($filters);

        return response()->json([
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ],
            'links' => [
                'first' => $tickets->url(1),
                'last' => $tickets->url($tickets->lastPage()),
                'prev' => $tickets->previousPageUrl(),
                'next' => $tickets->nextPageUrl(),
            ],
        ]);
    }

    public function show(int $id, TicketService $ticketService)
    {
        try {
            return response()->json([
                'data' => $ticketService->getTicketById($id),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Ticket no encontrado.'], 404);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function histories(Request $request, int $id, TicketService $ticketService)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $filters = TicketHistoryFiltersDto::fromArray($request->all());
            $histories = $ticketService->getHistoriesPaginated($ticket, $filters);

            return response()->json([
                'data' => $histories->items(),
                'meta' => [
                    'current_page' => $histories->currentPage(),
                    'last_page' => $histories->lastPage(),
                    'per_page' => $histories->perPage(),
                    'total' => $histories->total(),
                ],
                'links' => [
                    'first' => $histories->url(1),
                    'last' => $histories->url($histories->lastPage()),
                    'prev' => $histories->previousPageUrl(),
                    'next' => $histories->nextPageUrl(),
                ],
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Ticket no encontrado.'], 404);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(StoreTicketRequest $request, TicketService $ticketService)
    {
        $validated = $request->validated();
        $dto = StoreTicketDto::fromArray($validated);

        try {
            $ticket = $ticketService->storeTicket($dto);

            return response()->json([
                'message' => 'El ticket ha sido creado exitosamente.',
                'data' => $ticket,
            ], 201);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(int $id, StoreTicketRequest $request, TicketService $ticketService)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket no encontrado.'], 404);
        }

        $validated = $request->validated();
        $dto = StoreTicketDto::fromArray($validated);

        try {
            $ticket = $ticketService->updateTicket($ticket, $dto);

            return response()->json([
                'message' => 'El ticket ha sido actualizado exitosamente.',
                'data' => $ticket,
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function close(int $id, TicketService $ticketService)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket no encontrado.'], 404);
        }

        try {
            $data = $ticketService->closeTicketFromApi($ticket);

            return response()->json([
                'message' => $data['description'],
                'data' => $data['ticket'],
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy(int $id, TicketService $ticketService)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket no encontrado.'], 404);
        }

        try {
            $ticketService->deleteTicketFromApi($ticket);

            return response()->json([
                'message' => 'El ticket ha sido eliminado exitosamente.',
            ]);
        } catch (Throwable $e) {
            return $this->errorResponse($e);
        }
    }

    private function errorResponse(Throwable $e)
    {
        return response()->json([
            'error' => $e->getMessage(),
        ], $this->statusCodeFor($e));
    }

    private function statusCodeFor(Throwable $e): int
    {
        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return 404;
        }

        if ($e instanceof BadRequestException) {
            return 400;
        }

        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode();
        }

        return 500;
    }
}
