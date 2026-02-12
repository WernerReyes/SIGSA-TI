<?php
namespace App\Enums\Ticket;
enum TicketUrgency: string
{
    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';


    public static function values(): array
    {
        return array_map(fn(self $urgency) => $urgency->value, self::cases());
    }

    public static function implodeValues(): string
    {
        return implode(",", self::values());
    }


    public static function score(string $value): int
    {
        return match ($value) {
            self::LOW->value => 1,
            self::MEDIUM->value => 2,
            self::HIGH->value => 3,
            default => 0,
        };
    }
    

    public static function labels(): array
    {
        return [
            self::LOW->value => 'Baja',
            self::MEDIUM->value => 'Media',
            self::HIGH->value => 'Alta',

        ];
    }
    

    public static function label(string $value): string
    {
        return self::labels()[$value] ?? 'Desconocido';
    }
    
}