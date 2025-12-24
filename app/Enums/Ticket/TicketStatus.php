<?php
namespace App\Enums\Ticket;
enum TicketStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case RESOLVED = 'RESOLVED';
    case CLOSED = 'CLOSED';

    public static function values(): array
    {
        return array_map(fn(self $priority) => $priority->value, self::cases());
    }
}