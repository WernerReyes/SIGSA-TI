<?php
namespace App\DTOs\Ticket;

use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketRequestType;
use App\Enums\Ticket\TicketStatus;
use App\Enums\Ticket\TicketType;
use Auth;


class StoreTicketDto
{
    private function __construct(
        public readonly string $title,
        public readonly string $description,
        // public readonly ?int $technicianId,
        public readonly int $requesterId,
        public readonly TicketType $type,
        public readonly TicketPriority $priority,
        public readonly TicketStatus $status = TicketStatus::OPEN,
        public readonly ?TicketRequestType $requestType = null
    ) {
    }


    public static function fromArray(array $data): self
    {
        if ($data['type'] !== TicketType::SERVICE_REQUEST->value) {
            // $data['technician_id'] = null;
            $data['request_type'] = null;
        }

        return new self(
            title: $data['title'],
            description: $data['description'],
            // technicianId: isset($data['technician_id']) ? $data['technician_id'] : null,
            requesterId: Auth::id(),
            type: TicketType::from($data['type']),
            priority: TicketPriority::from($data['priority']),
            requestType: isset($data['request_type']) ? TicketRequestType::from($data['request_type']) : null

        );
    }

}