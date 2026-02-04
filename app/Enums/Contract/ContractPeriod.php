<?php

namespace App\Enums\Contract;

enum ContractPeriod: string
{
    case RECURRING = 'RECURRING';
    case FIXED_TERM = 'FIXED_TERM';
    case ONE_TIME = 'ONE_TIME';

    public static function values(): array
    {
        return array_map(fn (ContractPeriod $period) => $period->value, self::cases());
    }

    public static function implodeValues(string $delimiter = ','): string
    {
        return implode($delimiter, self::values());
    }
}


