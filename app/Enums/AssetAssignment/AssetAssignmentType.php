<?php
namespace App\Enums\AssetAssignment;

enum AssetAssignmentType: string
{
    case LAPTOP = 'LAPTOP';
    case CELL_PHONE = 'CELL_PHONE';
    
    public static function values(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }
}