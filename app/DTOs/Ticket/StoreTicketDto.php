<?php
namespace App\DTOs\Ticket;

use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketRequestType;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use Auth;


class StoreTicketDto
{
    private function __construct(
        public readonly string $title,
        public readonly string $description,
        // public readonly ?int $technicianId,
        public readonly int $requesterId,
        public readonly string $type,
        public readonly string $impact,
        public readonly string $urgency,
        public readonly ?string $category,
        public readonly string $priority,
        // public readonly TicketStatus $status = TicketStatus::OPEN,
        // public readonly ?TicketRequestType $requestType = null
    ) {
    }


    public static function fromArray(array $data): self
    {
        if ($data['type'] !== TicketType::SERVICE_REQUEST->value) {
            // $data['technician_id'] = null;
            $data['category'] = null;
        }

        return new self(
            title: $data['title'],
            description: $data['description'],
            requesterId: Auth::id(),
            type: $data['type'],
            impact: $data['impact'],
            urgency: $data['urgency'],
            category: $data['category'] ?? null,
            priority: TicketPriority::calculate(
                TicketImpact::from($data['impact']),
                TicketUrgency::from($data['urgency'])
            )->value,

        );
    }

}