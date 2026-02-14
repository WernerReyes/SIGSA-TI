<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\SlaMetricsService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function renderView(SlaMetricsService $metrics, DashboardService $dashboardService)
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return Inertia::render('Dashboard', [
            'stats' => Inertia::once( fn() => $dashboardService->stats()),
            'tickets_by_priority' => Inertia::once( fn() => $dashboardService->ticketsByPriority()),
            'recent_tickets' => Inertia::once( fn() => $dashboardService->recentTickets()),
            'weekly_sla_compliance' => Inertia::once( fn() => $dashboardService->getWeeklySlaCompliance()),
            'recent_contract_notifications' => Inertia::once( fn() => $dashboardService->recentContractNotifications())
        ]);
    }
}