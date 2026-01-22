<?php
namespace App\DTOs\Ticket;

class TicketFiltersDto
{
    private function __construct(
        public readonly ?string $searchTerm,
        public readonly ?array $requesters,
        public readonly ?array $responsibles,
        public readonly ?array $statuses = null,
        public readonly ?array $types = null,
        public readonly ?array $priorities = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            searchTerm: $data['searchTerm'] ?? null,
            requesters: $data['requesters'] ?? null,
            responsibles: $data['responsibles'] ?? null,
            statuses: $data['statuses'] ?? null,
            types: $data['types'] ?? null,
            priorities: $data['priorities'] ?? null
        );
    }

}