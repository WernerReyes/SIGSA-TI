<?php

namespace App\Enums\Ticket;

enum TicketHistoryAction: string
{
    case CREATED = 'CREATED';
    case UPDATED = 'UPDATED';
    case STATUS_CHANGED = 'STATUS_CHANGED';
    // case PRIORITY_CHANGED = 'PRIORITY_CHANGED';
    case ASSET_ASSIGNED = 'ASSET_ASSIGNED';
    // case COMMENTED = 'COMMENTED';

    public static function values(): array
    {
        return array_map(fn($action) => $action->value, self::cases());
    }
}
