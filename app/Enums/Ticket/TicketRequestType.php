<?php
namespace App\Enums\Ticket;
enum TicketRequestType: string
{
    case ACCESS = 'ACCESS';
    case SOFTWARE = 'SOFTWARE';
    case EQUIPMENT = 'EQUIPMENT';

    public static function values(): array
    {
        return array_map(fn(self $priority) => $priority->value, self::cases());
    }
}