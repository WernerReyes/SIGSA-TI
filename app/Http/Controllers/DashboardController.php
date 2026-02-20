<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\SlaMetricsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function renderView(Request $request, DashboardService $dashboardService)
    {

        $start = $request->input('start_date', now()->startOfWeek()->toDateString());
        $end = $request->input('end_date', now()->endOfWeek()->toDateString());

        return Inertia::render('Dashboard', [
            'stats' => Inertia::once(fn() => $dashboardService->stats()),
            'tickets_by_priority' => Inertia::once(fn() => $dashboardService->ticketsByPriority()),
            'recent_tickets' => Inertia::once(fn() => $dashboardService->recentTickets()),
            'sla_compliance' => Inertia::once(fn() => $dashboardService->getSlaComplianceByDateRange($start, $end)),
            'recent_contract_notifications' => Inertia::once(fn() => $dashboardService->recentContractNotifications())
        ]);
    }


    public function renderRRHHView(Request $request)
    {
        $service = new \App\Services\RRHHAssetsService();
        $assets = $service->getRRHHAssets();
        $stats = $service->getStats();
        return Inertia::render('DashboardRRHH', [
            'assets' => $assets,
            'stats' => $stats
        ]);
    }
}


// getSlaComplianceByDateRange