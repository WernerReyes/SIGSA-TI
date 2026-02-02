<?php
namespace App\Enums\AssetAssignment;

enum ReturnReason: string
{
    case TERMINATION_EMPLOYMENT = 'TERMINATION_EMPLOYMENT';
    case TECHNICAL_ISSUES = 'TECHNICAL_ISSUES';
    case EQUIPMENT_RENOVATION = 'EQUIPMENT_RENOVATION';

    case WRONG_ASSIGNMENT = 'WRONG_ASSIGNMENT';

    public static function values(): array
    {
        return array_map(fn(self $reason) => $reason->value, self::cases());
    }

    public static function labels(ReturnReason $reason = null): string|array
    {
        $labels = [
            self::TERMINATION_EMPLOYMENT->value => 'Cese Laboral',
            self::TECHNICAL_ISSUES->value => 'Servicio Técnico',
            self::EQUIPMENT_RENOVATION->value => 'Renovación de equipo',
        ];

        if ($reason) {
            return $labels[$reason->value];
        }

        return $labels;
    }


}