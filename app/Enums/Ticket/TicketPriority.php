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

    public static function implodeValues(string $separator = ','): string
    {
        return implode($separator, self::values());
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

    // public static function calculate(TicketImpact $impact, TicketUrgency $urgency): self
    // {
    //     $matrix = [
    //         TicketImpact::HIGH->value => [
    //             TicketUrgency::HIGH->value => self::URGENT,
    //             TicketUrgency::MEDIUM->value => self::HIGH,
    //             TicketUrgency::LOW->value => self::MEDIUM,
    //         ],

    //         TicketImpact::MEDIUM->value => [
    //             TicketUrgency::HIGH->value => self::HIGH,
    //             TicketUrgency::MEDIUM->value => self::MEDIUM,
    //             TicketUrgency::LOW->value => self::LOW,
    //         ],

    //         TicketImpact::LOW->value => [
    //             TicketUrgency::HIGH->value => self::MEDIUM,
    //             TicketUrgency::MEDIUM->value => self::LOW,
    //             TicketUrgency::LOW->value => self::LOW,
    //         ],
    //     ];

    //     return $matrix[$impact->value][$urgency->value] ?? self::LOW;
    // }


    public static function calculate(TicketImpact $impact, TicketUrgency $urgency): self
    {
        $score = TicketImpact::score($impact->value) * TicketUrgency::score($urgency->value);

        return match (true) {
            $score >= 9 => self::URGENT,
            $score >= 6 => self::HIGH,
            $score >= 4 => self::MEDIUM,
            default => self::LOW,
        };
    }



}

