<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Ticket\TicketDashboardFiltersDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\TicketDashboardRequest;
use App\Services\TicketDashboardService;
use Illuminate\Http\JsonResponse;

class TicketDashboardApiController extends Controller
{
    public function show(
        TicketDashboardRequest $request,
        TicketDashboardService $ticketDashboardService,
    ): JsonResponse {
        $filters = TicketDashboardFiltersDto::fromArray($request->validated());

        return response()->json([
            'data' => $ticketDashboardService->getDashboard($filters),
        ]);
    }
}
