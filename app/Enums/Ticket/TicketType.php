<?php
namespace App\Enums\Ticket;
enum TicketType: string
{
    case INCIDENT = 'INCIDENT';
    case SERVICE_REQUEST = 'SERVICE_REQUEST';

    public static function values(): array
    {
        return array_map(fn(self $priority) => $priority->value, self::cases());

    }
    public static function label(string $value): string
    {
        return self::labels()[$value] ?? 'Desconocido';
    }

    public static function labels(): array
    {
        return [
            self::INCIDENT->value => 'Incidente',
            self::SERVICE_REQUEST->value => 'Solicitud de Servicio',
        ];
    }
}