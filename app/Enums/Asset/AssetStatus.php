<?php
namespace App\Enums\Asset;

enum AssetStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case ASSIGNED = 'ASSIGNED';
    case IN_REPAIR = 'IN_REPAIR';
    case DECOMMISSIONED = 'DECOMMISSIONED';

    public static function values(): array
    {
        return array_map(fn(self $status) => $status->value, self::cases());
    }
}



