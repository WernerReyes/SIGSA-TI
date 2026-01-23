<?php
namespace App\Enums\Ticket;
enum TicketRequestType: string
{
    case ACCESS = 'ACCESS';
    case SOFTWARE = 'SOFTWARE';
    case EQUIPMENT = 'EQUIPMENT';

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
            self::ACCESS->value => 'Acceso',
            self::SOFTWARE->value => 'Software',
            self::EQUIPMENT->value => 'Equipo',
        ];
    }
}