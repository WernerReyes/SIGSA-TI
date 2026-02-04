<?php

namespace App\Enums\contract;

enum BillingFrequency: string
{
    case MONTHLY = 'MONTHLY';
    case QUARTERLY = 'QUARTERLY';
    case SEMIANNUAL = 'SEMIANNUAL';
    case ANNUAL = 'ANNUAL';
    case ONE_TIME = 'ONE_TIME';

    public static function values(): array
    {
        return array_map(fn (BillingFrequency $frequency) => $frequency->value, self::cases());
    }

    public static function implodeValues(string $delimiter = ','): string
    {
        return implode($delimiter, self::values());
    }
}

