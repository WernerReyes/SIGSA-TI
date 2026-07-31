<?php

namespace App\Services;

use App\DTOs\Ticket\TicketDashboardFiltersDto;
use App\Enums\Ticket\TicketCategory;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;

class TicketDashboardService
{
    public function getDashboard(TicketDashboardFiltersDto $filters): array
    {
        $query = $this->queryFor($filters);
        $tickets = $this->allTickets(clone $query);

        return [
            'filters' => $filters->toArray(),
            'summary' => $this->summary(clone $query),
            'by_status' => $this->distribution(
                clone $query,
                'status',
                TicketStatus::labels(),
            ),
            'by_priority' => $this->distribution(
                clone $query,
                'priority',
                TicketPriority::labels(),
            ),
            'by_type' => $this->distribution(
                clone $query,
                'type',
                TicketType::labels(),
            ),
            'by_category' => $this->categoryDistribution(clone $query),
            'daily_trend' => $this->dailyTrend($filters),
            'technicians' => $this->technicianPerformance(clone $query),
            'tickets' => $tickets,
            'recent_tickets' => array_slice($tickets, 0, 8),

        ];
    }

    public function getFilterOptions(): array
    {
        $responsibleIds = Ticket::query()
            ->whereNotNull('responsible_id')
            ->distinct()
            ->pluck('responsible_id');

        return [
            'responsibles' => User::query()
                ->select('staff_id', 'firstname', 'lastname')
                ->whereIn('staff_id', $responsibleIds)
                ->orderBy('firstname')
                ->orderBy('lastname')
                ->get(),
            'statuses' => collect(TicketStatus::labels())
                ->map(fn (string $label, string $value) => compact('value', 'label'))
                ->values(),
            'types' => collect(TicketType::labels())
                ->map(fn (string $label, string $value) => compact('value', 'label'))
                ->values(),
            'categories' => collect(TicketCategory::labels())
                ->map(fn (string $label, string $value) => compact('value', 'label'))
                ->values(),
        ];
    }

    private function queryFor(
        TicketDashboardFiltersDto $filters,
        string $dateColumn = 'created_at',
        bool $applyDateRange = true,
    ): Builder {
        return Ticket::query()
            ->when(
                $applyDateRange && $filters->startDate,
                fn (Builder $query) => $query->where(
                    $dateColumn,
                    '>=',
                    Carbon::parse($filters->startDate)->startOfDay(),
                ),
            )
            ->when(
                $applyDateRange && $filters->endDate,
                fn (Builder $query) => $query->where(
                    $dateColumn,
                    '<=',
                    Carbon::parse($filters->endDate)->endOfDay(),
                ),
            )
            ->when(
                $filters->responsibleId,
                fn (Builder $query, int $responsibleId) => $query->where('responsible_id', $responsibleId),
            )
            ->when(
                $filters->requesterId,
                fn (Builder $query, int $requesterId) => $query->where('requester_id', $requesterId),
            )
            ->when(
                $filters->status,
                fn (Builder $query, string $status) => $query->where('status', $status),
            )
            ->when(
                $filters->type,
                fn (Builder $query, string $type) => $query->where('type', $type),
            )
            ->when(
                $filters->category,
                fn (Builder $query, string $category) => $query->where('category', $category),
            );
    }

    private function summary(Builder $query): array
    {
        $resolvedQuery = clone $query;
        $slaBreachQuery = clone $query;

        $row = $query
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status NOT IN (?, ?) THEN 1 ELSE 0 END) as active', [
                TicketStatus::RESOLVED->value,
                TicketStatus::CLOSED->value,
            ])
            ->selectRaw('SUM(CASE WHEN resolved_at IS NOT NULL THEN 1 ELSE 0 END) as resolved')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as closed', [TicketStatus::CLOSED->value])
            ->selectRaw('SUM(CASE WHEN responsible_id IS NULL THEN 1 ELSE 0 END) as unassigned')
            ->first();

        $slaBreached = $slaBreachQuery
            ->where(function (Builder $query) {
                $query->where('sla_breached', true)
                    ->orWhere(function (Builder $activeOverdue) {
                        $activeOverdue
                            ->whereNull('resolved_at')
                            ->whereNotIn('status', [
                                TicketStatus::RESOLVED->value,
                                TicketStatus::CLOSED->value,
                            ])
                            ->whereNotNull('sla_resolution_due_at')
                            ->where('sla_resolution_due_at', '<', now());
                    });
            })
            ->count();

        $resolvedTickets = $resolvedQuery
            ->whereNotNull('resolved_at')
            ->get(['created_at', 'resolved_at', 'sla_paused_duration', 'sla_breached']);

        $resolvedCount = $resolvedTickets->count();
        $compliedCount = $resolvedTickets->where('sla_breached', false)->count();
        $averageResolutionMinutes = $resolvedCount === 0
            ? 0
            : (int) round($resolvedTickets->average(function (Ticket $ticket) {
                $elapsed = Carbon::parse($ticket->created_at)->diffInMinutes(Carbon::parse($ticket->resolved_at));

                return max(0, $elapsed - (float) $ticket->sla_paused_duration);
            }));

        return [
            'total' => (int) ($row->total ?? 0),
            'active' => (int) ($row->active ?? 0),
            'resolved' => (int) ($row->resolved ?? 0),
            'closed' => (int) ($row->closed ?? 0),
            'unassigned' => (int) ($row->unassigned ?? 0),
            'sla_breached' => $slaBreached,
            'sla_compliance_rate' => $resolvedCount > 0
                ? round(($compliedCount / $resolvedCount) * 100, 1)
                : 0.0,
            'average_resolution_minutes' => $averageResolutionMinutes,
        ];
    }

    private function distribution(Builder $query, string $column, array $labels): array
    {
        $counts = $query
            ->selectRaw("{$column} as value, COUNT(*) as total")
            ->groupBy($column)
            ->pluck('total', 'value');
        $total = (int) $counts->sum();

        return collect($labels)
            ->map(fn (string $label, string $value) => [
                'value' => $value,
                'label' => $label,
                'count' => (int) ($counts[$value] ?? 0),
                'percentage' => $total > 0
                    ? round(((int) ($counts[$value] ?? 0) / $total) * 100, 1)
                    : 0.0,
            ])
            ->values()
            ->all();
    }

    private function categoryDistribution(Builder $query): array
    {
        $counts = $query
            ->selectRaw("COALESCE(category, 'UNCATEGORIZED') as value, COUNT(*) as total")
            ->groupBy('category')
            ->pluck('total', 'value');
        $total = (int) $counts->sum();
        $labels = [...TicketCategory::labels(), 'UNCATEGORIZED' => 'Sin categoría'];

        return collect($labels)
            ->map(fn (string $label, string $value) => [
                'value' => $value,
                'label' => $label,
                'count' => (int) ($counts[$value] ?? 0),
                'percentage' => $total > 0
                    ? round(((int) ($counts[$value] ?? 0) / $total) * 100, 1)
                    : 0.0,
            ])
            ->values()
            ->all();
    }

    private function dailyTrend(TicketDashboardFiltersDto $filters): array
    {
        $range = $this->resolveTrendRange($filters);

        if ($range === null) {
            return [];
        }

        $created = $this->dailyCounts($this->queryFor($filters), 'created_at');
        $resolved = $this->dailyCounts(
            $this->queryFor($filters, 'resolved_at')->whereNotNull('resolved_at'),
            'resolved_at',
        );

        return collect(CarbonPeriod::create($range['start'], $range['end']))
            ->map(function (Carbon $date) use ($created, $resolved) {
                $day = $date->toDateString();

                return [
                    'date' => $day,
                    'created' => (int) ($created[$day] ?? 0),
                    'resolved' => (int) ($resolved[$day] ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    private function resolveTrendRange(TicketDashboardFiltersDto $filters): ?array
    {
        $query = $this->queryFor($filters, applyDateRange: false);

        $earliestCreatedAt = (clone $query)->min('created_at');
        $latestCreatedAt = (clone $query)->max('created_at');
        $latestResolvedAt = (clone $query)->max('resolved_at');

        $start = $filters->startDate
            ? Carbon::parse($filters->startDate)->startOfDay()
            : ($earliestCreatedAt ? Carbon::parse($earliestCreatedAt)->startOfDay() : null);

        $latestEventAt = collect([$latestCreatedAt, $latestResolvedAt])
            ->filter()
            ->map(fn ($date) => Carbon::parse($date))
            ->sortDesc()
            ->first();

        $end = $filters->endDate
            ? Carbon::parse($filters->endDate)->endOfDay()
            : $latestEventAt?->endOfDay();

        if (! $start || ! $end || $start->gt($end)) {
            return null;
        }

        return compact('start', 'end');
    }

    private function dailyCounts(Builder $query, string $column)
    {
        return $query
            ->selectRaw("DATE({$column}) as day, COUNT(*) as total")
            ->groupByRaw("DATE({$column})")
            ->pluck('total', 'day');
    }

    private function technicianPerformance(Builder $query): array
    {
        $rows = $query
            ->whereNotNull('responsible_id')
            ->select('responsible_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN resolved_at IS NOT NULL THEN 1 ELSE 0 END) as resolved')
            ->selectRaw('SUM(CASE WHEN status NOT IN (?, ?) THEN 1 ELSE 0 END) as active', [
                TicketStatus::RESOLVED->value,
                TicketStatus::CLOSED->value,
            ])
            ->selectRaw('SUM(CASE WHEN sla_breached = 1 THEN 1 ELSE 0 END) as sla_breached')
            ->groupBy('responsible_id')
            ->orderByDesc('resolved')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $users = User::query()
            ->select('staff_id', 'firstname', 'lastname')
            ->whereIn('staff_id', $rows->pluck('responsible_id'))
            ->get()
            ->keyBy('staff_id');

        return $rows->map(function ($row) use ($users) {
            $user = $users->get($row->responsible_id);

            return [
                'staff_id' => (int) $row->responsible_id,
                'name' => $user?->full_name ?? 'Usuario no disponible',
                'total' => (int) $row->total,
                'resolved' => (int) $row->resolved,
                'active' => (int) $row->active,
                'sla_breached' => (int) $row->sla_breached,
                'resolution_rate' => (int) $row->total > 0
                    ? round(((int) $row->resolved / (int) $row->total) * 100, 1)
                    : 0.0,
            ];
        })->values()->all();
    }

    private function allTickets(Builder $query): array
    {
        $query
            ->with([
                'requester:staff_id,firstname,lastname',
                'responsible:staff_id,firstname,lastname',
            ])
            ->latest('created_at');

        return $query->get([
            'id',
            'title',
            'description',
            'status',
            'priority',
            'type',
            'category',
            'images',
            'requester_id',
            'responsible_id',
            'created_at',
        ])
            ->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'type' => $ticket->type,
                'images' => $ticket->images,
                'category' => $ticket->category,
                'requester_id' => $ticket->requester_id,
                'responsible_id' => $ticket->responsible_id,
                'requester' => $ticket->requester,
                'responsible' => $ticket->responsible,
                'created_at' => $ticket->created_at,
            ])
            ->all();
    }
}
