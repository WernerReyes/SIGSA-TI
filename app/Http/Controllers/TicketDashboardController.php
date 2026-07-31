<?php

namespace App\Http\Controllers;

use App\DTOs\Ticket\TicketDashboardFiltersDto;
use App\Http\Requests\Ticket\TicketDashboardRequest;
use App\Services\TicketDashboardService;
use Inertia\Inertia;
use Inertia\Response;

class TicketDashboardController extends Controller
{
    public function renderView(
        TicketDashboardRequest $request,
        TicketDashboardService $ticketDashboardService,
    ): Response {
        $validated = $request->validated();

        if (! $request->has('start_date') && ! $request->has('end_date')) {
            $validated['start_date'] = now()->subDays(29)->toDateString();
            $validated['end_date'] = now()->toDateString();
        }

        $filters = TicketDashboardFiltersDto::fromArray($validated);

        return Inertia::render('TicketDashboard', [
            'dashboard' => $ticketDashboardService->getDashboard($filters),
            'filterOptions' => $ticketDashboardService->getFilterOptions(),
        ]);
    }
}
