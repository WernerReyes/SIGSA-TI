<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SlaMetricsService;
use Inertia\Inertia;
use function PHPUnit\Framework\returnArgument;
class DashboardController extends Controller
{
    public function renderView(SlaMetricsService $metrics)
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        return Inertia::render('Dashboard', [
            'metrics' => [
                'sla_compliance' => $metrics->getMonthlyCompliance(),
                'mttr_minutes' => $metrics->calculateMttr($start, $end),
                'total_resolved' => $metrics->totalResolved($start, $end),
                'breached' => $metrics->totalBreached($start, $end),
            ],
            'charts' => [
                'daily_compliance' => $metrics->dailyCompliance(),
                'monthly_trend' => $metrics->monthlyTrend(),
                'by_priority' => $metrics->complianceByPriority($start, $end),
            ]
        ]);
    }
}