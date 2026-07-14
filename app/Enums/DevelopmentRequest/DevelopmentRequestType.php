<?php

namespace App\Enums\DevelopmentRequest;

enum DevelopmentRequestType: string
{
    case NEW_PROJECT = 'NEW_PROJECT';
    case NEW_MODULE = 'NEW_MODULE';

    case NEW_FEATURE = 'NEW_FEATURE';

    public static function values(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }

    public static function implode(string $separator = ','): string
    {
        return implode($separator, self::values());
    }

    public static function label(self|string|null $type): string
    {
        $value = $type instanceof self ? $type->value : $type;

        return match ($value) {
            self::NEW_PROJECT->value => 'Nuevo proyecto',
            self::NEW_MODULE->value => 'Nuevo modulo',
            self::NEW_FEATURE->value => 'Nueva funcionalidad',
            default => 'Desconocido',
        };
    }
}
