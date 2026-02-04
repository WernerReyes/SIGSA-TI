<?php

namespace App\Enums\Contract;

enum ContractStatus: string
{
    case ACTIVE = 'ACTIVE';
    case EXPIRED = 'EXPIRED';
    case CANCELED = 'CANCELED';

    public static function values(): array
    {
        return array_map(fn(ContractStatus $status) => $status->value, self::cases());
    }

    public static function implodeValues(string $delimiter = ','): string
    {
        return implode($delimiter, self::values());
    }
}
