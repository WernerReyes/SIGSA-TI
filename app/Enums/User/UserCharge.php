<?php
namespace App\Enums\User;

enum UserCharge: int
{
    case TI_MANAGER = 7;
    case TI_ASSISTANT_MANAGER = 9;

    public static function values(): array
    {
        return array_map(fn(self $charge) => $charge->value, self::cases());
    }

    public static function implodeValues(string $separator = ','): string
    {
        return implode($separator, self::values());
    }

    public static function includes(int $charge): bool
    {
        return in_array($charge, self::values());
    }

}

// export enum UserCharge {
//     IT_MANAGER = 7,
//     IT_ASSISTANT_MANAGER = 9,
// }
