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

    public static function label(self|string|null $priority): string
    {
        $value = $priority instanceof self ? $priority->value : $priority;

        return match ($value) {
            self::LOW->value => 'Baja',
            self::MEDIUM->value => 'Media',
            self::HIGH->value => 'Alta',
            self::URGENT->value => 'Urgente',
            default => $value ?? 'N/A',
        };
    }
}
