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
            'stats' => $dashboardService->stats(),
            'tickets_by_priority' => $dashboardService->ticketsByPriority(),
            'recent_tickets' => $dashboardService->recentTickets(),
            'weekly_sla_compliance' => $dashboardService->getWeeklySlaCompliance(),
            'recent_contract_notifications' => $dashboardService->recentContractNotifications(),

            // 'metrics' => [
            //     'sla_compliance' => $metrics->getMonthlyCompliance(),
            //     'mttr_minutes' => $metrics->calculateMttr($start, $end),
            //     'total_resolved' => $metrics->totalResolved($start, $end),
            //     'breached' => $metrics->totalBreached($start, $end),
            // ],
            // 'charts' => [
            //     'daily_compliance' => $metrics->dailyCompliance(),
            //     'monthly_trend' => $metrics->monthlyTrend(),
            //     'by_priority' => $metrics->complianceByPriority($start, $end),
            // ]
        ]);
    }
}