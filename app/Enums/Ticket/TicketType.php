<?php
namespace App\Enums\Ticket;
enum TicketType: string
{
    case INCIDENT = 'INCIDENT';
    case SERVICE_REQUEST = 'SERVICE_REQUEST';

    public static function values(): array
    {
        return array_map(fn(self $priority) => $priority->value, self::cases());
    }
}