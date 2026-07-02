<?php
namespace App\Enums\User;

enum UserDept: int
{
    case TI = 11;

    case RRHH = 17;

    public static function values(): array
    {
        return array_map(fn(self $dept) => $dept->value, self::cases());
    }

    public static function implodeValues(string $separator = ','): string
    {
        return implode($separator, self::values());
    }

    public static function includes(int $dept): bool
    {
        return in_array($dept, self::values());
    }

}