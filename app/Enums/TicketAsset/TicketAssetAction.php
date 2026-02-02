<?php

namespace App\Enums\TicketAsset;

enum TicketAssetAction: string
{

    case ASSIGNED = 'ASSIGNED';
    case RETURNED = 'RETURNED';

    case CHANGED = 'CHANGED';
    public static function values(): array
    {
        return array_map(fn($status) => $status->value, self::cases());
    }

    
    
}
