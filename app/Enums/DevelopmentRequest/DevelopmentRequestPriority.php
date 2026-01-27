<?php

namespace App\Enums\DevelopmentRequest;

enum DevelopmentRequestPriority: string
{
    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';
    case URGENT = 'URGENT';

    public static function values(): array
    {
        return array_map(fn(self $priority) => $priority->value, self::cases());
    }

    public static function implode(string $separator = ','): string
    {
        return implode($separator, self::values());
    }
}