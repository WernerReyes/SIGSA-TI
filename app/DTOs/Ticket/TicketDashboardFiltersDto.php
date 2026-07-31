<?php

namespace App\DTOs\Ticket;

use Carbon\CarbonImmutable;

class TicketDashboardFiltersDto
{
    private function __construct(
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly ?int $responsibleId,
        public readonly ?int $requesterId,
        public readonly ?string $status,
        public readonly ?string $type,
        public readonly ?string $category,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            startDate: isset($data['start_date'])
                ? CarbonImmutable::parse($data['start_date'])->toDateString()
                : null,
            endDate: isset($data['end_date'])
                ? CarbonImmutable::parse($data['end_date'])->toDateString()
                : null,
            responsibleId: isset($data['responsible_id']) ? (int) $data['responsible_id'] : null,
            requesterId: isset($data['requester_id']) ? (int) $data['requester_id'] : null,
            status: $data['status'] ?? null,
            type: $data['type'] ?? null,
            category: $data['category'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'responsible_id' => $this->responsibleId,
            'requester_id' => $this->requesterId,
            'status' => $this->status,
            'type' => $this->type,
            'category' => $this->category,
        ];
    }
}
