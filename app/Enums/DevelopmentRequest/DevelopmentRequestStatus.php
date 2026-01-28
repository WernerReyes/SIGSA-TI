<?php

namespace App\Enums\DevelopmentRequest;

enum DevelopmentRequestStatus: string
{
    case REGISTERED = 'REGISTERED';
    case IN_ANALYSIS = 'IN_ANALYSIS';
    case APPROVED = 'APPROVED';
    case IN_DEVELOPMENT = 'IN_DEVELOPMENT';

    case IN_TESTING = 'IN_TESTING';

    case COMPLETED = 'COMPLETED';

    case REJECTED = 'REJECTED';

    public static function values(): array
    {
        return array_map(fn (self $status) => $status->value, self::cases());
    }

    public static function labels(): array
    {
        return [
            self::REGISTERED->value => 'Registrada',
            self::IN_ANALYSIS->value => 'En Análisis',
            self::APPROVED->value => 'Aprobada',
            self::IN_DEVELOPMENT->value => 'En Desarrollo',
            self::IN_TESTING->value => 'En Pruebas',
            self::COMPLETED->value => 'Completada',
            self::REJECTED->value => 'Rechazada',
        ];
    }



    public static function implodeValues(string $separator = ','): string
    {
        return implode($separator, self::values());
    }
}
