<?php
namespace App\Enums\Ticket;
enum TicketStatus: string
{
    case OPEN = 'OPEN';
    case IN_PROGRESS = 'IN_PROGRESS';
    case RESOLVED = 'RESOLVED';
    case CLOSED = 'CLOSED';

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
            self::OPEN->value => 'Abierto',
            self::IN_PROGRESS->value => 'En Progreso',
            self::RESOLVED->value => 'Resuelto',
            self::CLOSED->value => 'Cerrado',
        ];
    }
}