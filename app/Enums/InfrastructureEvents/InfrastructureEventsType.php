<?php

namespace App\Enums\InfrastructureEvents;



enum InfrastructureEventsType: string
{
    case CHANGE = 'CHANGE';
    case MAINTENANCE = 'MAINTENANCE';
    case SECURITY = 'SECURITY';
    case AUDIT = 'AUDIT';
    case INCIDENT = 'INCIDENT';
    case RENEWAL = 'RENEWAL';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function implodeValues(string $separator = ','): string
    {
        return implode($separator, self::values());
    }
}


// 'CHANGE', 'MAINTENANCE', 'SECURITY', 'AUDIT', 'INCIDENT', 'RENEWAL'