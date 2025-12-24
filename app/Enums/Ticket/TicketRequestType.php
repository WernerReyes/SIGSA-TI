<?php
namespace App\Enums\Ticket;
enum TicketRequestType: string
{
    case HARDWARE = 'HARDWARE';
    case SOFTWARE = 'SOFTWARE';
    case NETWORK = 'NETWORK';
    case OTHER = 'OTHER';

    public static function values(): array
    {
        return array_map(fn(self $priority) => $priority->value, self::cases());
    }
}