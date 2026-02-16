<?php
namespace App\DTOs\Ticket;

use App\Enums\Ticket\TicketImpact;
use App\Enums\Ticket\TicketPriority;
use App\Enums\Ticket\TicketType;
use App\Enums\Ticket\TicketUrgency;
use Auth;


class StoreTicketDto
{
    private function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?array $images,
        public readonly int $requesterId,
        public readonly string $type,
        public readonly string $impact,
        public readonly string $urgency,
        public readonly ?string $category,
        public readonly string $priority,
        
    ) {
    }


    public static function fromArray(array $data): self
    {
        if ($data['type'] !== TicketType::SERVICE_REQUEST->value) {
            $data['category'] = null;
        }

        return new self(
            title: $data['title'],
            description: $data['description'],
            requesterId: $data['requester_id'] ?? Auth::id(),
            images: $data['images'] ?? null,
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