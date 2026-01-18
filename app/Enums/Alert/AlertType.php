<?php
namespace App\Enums\Alert;
enum AlertType: string
{
    case ACCESSORY_OUT_OF_STOCK = 'accessory_out_of_stock';

    public static function values(): array
    {
        return array_map(fn(AlertType $type) => $type->value, self::cases());
    }
}