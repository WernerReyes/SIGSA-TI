<?php

namespace App\Services;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SlaMetricsService
{
 

    /*
    |--------------------------------------------------------------------------
    | MTTR (con horario laboral)
    |--------------------------------------------------------------------------
    */

    public function calculateMttr(Carbon $start, Carbon $end): float
    {
        $tickets = Ticket::whereBetween('resolved_at', [$start, $end])
            ->whereNotNull('resolved_at')
            ->get();

        if ($tickets->count() === 0) {
            return 0;
        }

        $totalMinutes = 0;

        foreach ($tickets as $ticket) {
            $totalMinutes += app(BusinessHoursService::class)->calculateBusinessMinutesBetween(
                Carbon::parse($ticket->created_at),
                Carbon::parse($ticket->resolved_at)
            );
        }

        return round($totalMinutes / $tickets->count(), 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Totales
    |--------------------------------------------------------------------------
    */

    public function totalResolved(Carbon $start, Carbon $end): int
    {
        return Ticket::whereBetween('resolved_at', [$start, $end])
            ->count();
    }

    public function totalBreached(Carbon $start, Carbon $end): int
    {
        return Ticket::whereBetween('resolved_at', [$start, $end])
            ->where('sla_breached', true)
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | SLA Compliance %
    |--------------------------------------------------------------------------
    */

    public function getMonthlyCompliance(): float
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        $total = $this->totalResolved($start, $end);
        $breached = $this->totalBreached($start, $end);

        return $total > 0
            ? round((($total - $breached) / $total) * 100, 2)
            : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Charts
    |--------------------------------------------------------------------------
    */

    public function dailyCompliance(int $days = 30)
    {
        ds(Ticket::selectRaw("
                DATE(resolved_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN sla_breached = 0 THEN 1 ELSE 0 END) as complied
            ") ->where('resolved_at', '>=', now()->subDays($days)) ->groupBy('date')->get());

        return Ticket::selectRaw("
                DATE(resolved_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN sla_breached = 0 THEN 1 ELSE 0 END) as complied
            ")
            ->where('resolved_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->date,
                    'compliance' => $row->total > 0
                        ? round(($row->complied / $row->total) * 100, 2)
                        : 0
                ];
            });
    }

    public function monthlyTrend(int $months = 12)
    {
// ds(now()->subMonths($months));

        return Ticket::selectRaw("
                YEAR(resolved_at) as year,
                MONTH(resolved_at) as month,
                COUNT(*) as total,
                SUM(CASE WHEN sla_breached = 0 THEN 1 ELSE 0 END) as complied
            ")
            ->where('resolved_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                
                return [
                    'year' => $row->year,
                    'month' => $row->month,
                    'compliance' => $row->total > 0
                        ? round(($row->complied / $row->total) * 100, 2)
                        : 0
                ];
            });
    }

    public function complianceByPriority(Carbon $start, Carbon $end)
    {
        return Ticket::selectRaw("
                priority,
                COUNT(*) as total,
                SUM(CASE WHEN sla_breached = 0 THEN 1 ELSE 0 END) as complied
            ")
            ->whereBetween('resolved_at', [$start, $end])
            ->groupBy('priority')
            ->get()
            ->map(function ($row) {
                return [
                    'priority' => $row->priority,
                    'compliance' => $row->total > 0
                        ? round(($row->complied / $row->total) * 100, 2)
                        : 0
                ];
            });
    }
}
