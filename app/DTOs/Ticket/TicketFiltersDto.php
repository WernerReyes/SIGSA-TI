<?php
namespace App\DTOs\Ticket;

class TicketFiltersDto
{
    private function __construct(
        public readonly ?string $searchTerm,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            searchTerm: $data['searchTerm'] ?? null,
        );
    }

}