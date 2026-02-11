<?php

namespace App\Services;

use App\Enums\Ticket\TicketPriority;
use App\Models\SlaPolicy;
use Illuminate\Support\Facades\DB;

class SlaPolicyService
{
    public function getSlaPolicies(): array
    {
        $slas = SlaPolicy::query()
            ->whereIn('priority', TicketPriority::values())
            ->get()
            ->keyBy('priority');

        return collect(TicketPriority::values())
            ->map(function (string $priority) use ($slas) {
                $sla = $slas->get($priority);

                return [
                    'priority' => $priority,
                    'label' => TicketPriority::label($priority),
                    'response_time_minutes' => (int) ($sla?->response_time_minutes ?? 0),
                    'resolution_time_minutes' => (int) ($sla?->resolution_time_minutes ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    public function updateSlaPolicy(array $slas): void
    {
        $payload = collect($slas)
            ->map(function (array $sla) {
                return [
                    'priority' => $sla['priority'],
                    'response_time_minutes' => (int) $sla['response_time_minutes'],
                    'resolution_time_minutes' => (int) $sla['resolution_time_minutes'],
                ];
            })
            ->values()
            ->all();

        DB::transaction(function () use ($payload) {
            SlaPolicy::upsert(
                $payload,
                ['priority'],
                ['response_time_minutes', 'resolution_time_minutes']
            );
        });
    }
}
