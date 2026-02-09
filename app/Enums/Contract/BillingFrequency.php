<?php

namespace App\Enums\Contract;

enum BillingFrequency: string
{
    case MONTHLY = 'MONTHLY';
    case QUARTERLY = 'QUARTERLY';
    case SEMIANNUAL = 'SEMIANNUAL';
    case ANNUAL = 'ANNUAL';
    case ONE_TIME = 'ONE_TIME';

    public static function values(): array
    {
        return array_map(fn(BillingFrequency $frequency) => $frequency->value, self::cases());
    }

    public static function implodeValues(string $delimiter = ','): string
    {
        return implode($delimiter, self::values());
    }

    public static function labels(): array
    {
        return [
            self::MONTHLY->value => 'cada mes',
            self::QUARTERLY->value => 'cada 3 meses',
            self::SEMIANNUAL->value => 'cada 6 meses',
            self::ANNUAL->value => 'cada año',
            self::ONE_TIME->value => 'Una vez',
        ];
    }

    public static function label(string $frequency): ?string
    {
        $labels = self::labels();
        return $labels[$frequency] ?? null;
    }

    public static function days(): array
    {
        return [
            self::MONTHLY->value => 30,
            self::QUARTERLY->value => 90,
            self::SEMIANNUAL->value => 180,
            self::ANNUAL->value => 365,
            self::ONE_TIME->value => null,
        ];
    }

    public static function months(): array
    {
        return [
            self::MONTHLY->value => 1,
            self::QUARTERLY->value => 3,
            self::SEMIANNUAL->value => 6,
            self::ANNUAL->value => 12,
            self::ONE_TIME->value => null,
        ];
    }
    public static function getDay(?string $frequency): ?int
    {   
        if (!$frequency) { return null;
        }
        $days = self::days();
        return $days[$frequency] ?? null;
    }

    public static function getMonth(string $frequency): ?int
    {
        $months = self::months();
        return $months[$frequency] ?? null;
    }
}

    