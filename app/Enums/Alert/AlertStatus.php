<?php

namespace App\Enums\Alert;

enum AlertStatus: string
{
    case ACTIVE = 'ACTIVE';
    case RESOLVED = 'RESOLVED';

    public static function  values(): array
    {
        return array_map(fn(AlertStatus $status) => $status->value, self::cases());
    }
}
