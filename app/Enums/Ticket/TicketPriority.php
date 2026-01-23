<?php
namespace App\Enums\Ticket;
enum TicketPriority: string
{
    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';
    case URGENT = 'URGENT';

    public static function values(): array
    {
        return array_map(fn(self $priority) => $priority->value, self::cases());
    }

    public static function labels(): array
    {
        return [
            self::LOW->value => 'Baja',
            self::MEDIUM->value => 'Media',
            self::HIGH->value => 'Alta',
            self::URGENT->value => 'Urgente',
        ];
    }

    public static function label(string $value): string
    {
        return self::labels()[$value] ?? 'Desconocido'; 
    }
}