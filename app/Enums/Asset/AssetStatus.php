<?php
namespace App\Enums\Asset;

enum AssetStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case ASSIGNED = 'ASSIGNED';
    case IN_REPAIR = 'IN_REPAIR';
    case DECOMMISSIONED = 'DECOMMISSIONED';

    public static function values(array $exclude = []): array
    {
        return array_filter(
            array_map(fn (AssetStatus $status) => $status->value, self::cases()),
            fn (string $value) => !in_array($value, $exclude)
        );
    }
}



