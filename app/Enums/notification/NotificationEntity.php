<?php

namespace App\Enums\notification;

enum NotificationEntity: string
{
    case CONTRACT = "CONTRACT";

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function implodeValues($separator = ','): string
    {
        return implode($separator, self::values());
    }
}
