<?php
namespace App\Enums\DevelopmentRequest;

enum DevelopmentApprovalLevel: string
{
    case TECHNICAL = 'TECHNICAL';
    case STRATEGIC = 'STRATEGIC';

    public static function values(): array
    {
        return array_map(fn(self $level) => $level->value, self::cases());
    }

    public static function implodeValues(string $separator = ','): string
    {
        return implode($separator, self::values());
    }

}