<?php
namespace App\Services;

use App\Enums\Asset\AssetStatus;
use App\Enums\Department\Allowed;
use App\Enums\DevelopmentRequest\DevelopmentRequestStatus;
use App\Enums\notification\NotificationEntity;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketStatus;
use App\Models\Asset;
use App\Models\DevelopmentRequest;
use App\Models\Notification;
use App\Models\Ticket;
use Carbon\Carbon;

class DashboardService
{
    public function stats()
    {
        return [
            'open_tickets' => $this->getOpenTicketStats(),
            'sla_breaches' => $this->getSlaBreachStats(),
            'assets_at_risk' => $this->getAssetsAtRiskStats(),
            'active_projects' => $this->getActiveProjectsStats(),
        ];
    }

    private function getOpenTicketStats(): array
    {
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $previousMonthStart = $now->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $previousMonthStart->copy()->endOfMonth();

        $stats = Ticket::selectRaw("
            COUNT(*) as total_open,
            SUM(CASE WHEN responsible_id IS NULL THEN 1 ELSE 0 END) as unassigned,
            SUM(CASE 
                WHEN created_at >= ? THEN 1 
                ELSE 0 
            END) as current_month,
            SUM(CASE 
                WHEN created_at BETWEEN ? AND ? THEN 1 
                ELSE 0 
            END) as previous_month
        ", [
            $currentMonthStart,
            $previousMonthStart,
            $previousMonthEnd
        ])
            ->where('status', '!=', TicketStatus::CLOSED->value)
            ->first();

        $percentage = 0;

        if ($stats->previous_month > 0) {
            $percentage = (
                ($stats->current_month - $stats->previous_month)
                / $stats->previous_month
            ) * 100;
        }

        return [
            'total' => (int) $stats->total_open,
            'unassigned' => (int) $stats->unassigned,
            'trend_percentage' => round($percentage, 1),
            'trend_direction' => $percentage < 0 ? 'down' : 'up',
        ];
    }

    private function getSlaBreachStats(): array
    {
        $now = now();

        $stats = Ticket::selectRaw("
            COUNT(*) as out_sla,
            SUM(CASE WHEN priority = ? THEN 1 ELSE 0 END) as critical_count
        ", [TicketPriority::URGENT->value])
            ->where('status', '!=', TicketStatus::CLOSED->value)
            ->whereNotNull('sla_resolution_due_at')
            ->where('sla_resolution_due_at', '<', $now)
            ->first();

        $severity = match (true) {
            $stats->critical_count > 0 => TicketPriority::URGENT->value,
            $stats->out_sla >= 10 => TicketPriority::HIGH->value,
            $stats->out_sla > 0 => TicketPriority::MEDIUM->value,
            default => TicketPriority::LOW->value,
        };

        return [
            'out_sla' => (int) $stats->out_sla,
            'severity' => $severity,
            'message' => $stats->out_sla > 0
                ? 'Requieren atención inmediata'
                : 'Sin incumplimientos de SLA',
        ];
    }


    private function getAssetsAtRiskStats(): array
    {
        $now = now()->startOfDay();
        $next30Days = now()->addDays(30)->endOfDay();
        $last30Days = now()->subDays(30)->startOfDay();

        $stats = Asset::selectRaw("
            COUNT(*) as at_risk,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_assets
        ", [$last30Days])
            ->where('status', "!=", AssetStatus::DECOMMISSIONED->value)
            ->whereNotNull('warranty_expiration')
            ->whereBetween('warranty_expiration', [$now, $next30Days])
            ->first();

        return [
            'total' => (int) $stats->at_risk,
            'new_assets' => (int) $stats->new_assets,
            'message' => $stats->at_risk > 0
                ? 'Garantías próximas a vencer'
                : 'Sin riesgos detectados',
        ];
    }


    private function getActiveProjectsStats(): array
    {
        $now = now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $previousMonthStart = $now->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $previousMonthStart->copy()->endOfMonth();

        $stats = DevelopmentRequest::selectRaw("
            COUNT(*) as total_active,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as in_development,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as current_month,
            SUM(CASE WHEN created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as previous_month
        ", [
            DevelopmentRequestStatus::IN_DEVELOPMENT->value,
            $currentMonthStart,
            $previousMonthStart,
            $previousMonthEnd
        ])
            ->where('status', '!=', DevelopmentRequestStatus::COMPLETED->value)
            ->first();

        $percentage = $stats->previous_month > 0
            ? (($stats->current_month - $stats->previous_month) / $stats->previous_month) * 100
            : 0;

        return [
            'total' => (int) $stats->total_active,
            'in_development' => (int) $stats->in_development,
            'trend_percentage' => round($percentage, 1),
            'trend_direction' => $percentage < 0 ? 'down' : 'up',
        ];
    }


    public function ticketsByPriority()
    {

        return Ticket::selectRaw('priority, COUNT(*) as count')
            // ->where('status', '!=', TicketStatus::CLOSED->value)
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->priority => $item->count];
            });
    }

    public function recentTickets()
    {
        return Ticket::with('requester:staff_id,firstname,lastname')
            ->orderBy('created_at', 'desc')
            ->limit(5)->get();
    }



    public function getSlaComplianceByDateRange(string $from, string $to): array
    {
        $startDate = Carbon::parse($from)->startOfDay();
        $endDate = Carbon::parse($to)->endOfDay();

        $data = Ticket::selectRaw("
            DATE(resolved_at) as day,
            SUM(CASE WHEN sla_breached = 0 THEN 1 ELSE 0 END) as complied,
            SUM(CASE WHEN sla_breached = 1 THEN 1 ELSE 0 END) as breached
        ")
            ->whereBetween('resolved_at', [$startDate, $endDate])
            ->whereNotNull('sla_resolution_due_at')
            ->where('status', TicketStatus::CLOSED->value)
            ->groupByRaw("DATE(resolved_at)")
            ->orderBy("day")
            ->get();

        $result = [];

        // Fill in missing dates with 0 compliance
        $period = Carbon::parse($from)->daysUntil(Carbon::parse($to));
        foreach ($period as $date) {
            $dayData = $data->firstWhere('day', $date->toDateString());

            $complied = $dayData ? (int) $dayData->complied : 0;
            $breached = $dayData ? (int) $dayData->breached : 0;
            $total = $complied + $breached;

            $result[] = [
                'date' => $date->toDateString(),
                'complied' => $complied,
                'breached' => $breached,
                'compliance_rate' => $total > 0 ? round(($complied / $total) * 100, 1) : 0,
                'breach_rate' => $total > 0 ? round(($breached / $total) * 100, 1) : 0,
            ];
        }

        return [
            'range' => [
                'from' => $startDate->toDateString(),
                'to' => $endDate->toDateString(),
            ],
            'daily' => $result
        ];
    }



    public function recentContractNotifications()
    {
     
        if (auth()->user()->dept_id !== Allowed::SYSTEM_TI->value) {
            return [];
        }

        return Notification::
            with('contract')->
            where('type', NotificationEntity::CONTRACT->value)
            ->where('read_at', null)
            ->where('notifiable_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }


}