<?php
namespace App\Enums\Asset;
enum AssetTypeCategory: string
{
    case LAPTOP = 'LAPTOP';
    case ACCESSORY = 'ACCESSORY';

    case CELL_PHONE = 'CELL_PHONE';

    

    public static function values(): array
    {
        return array_map(fn(self $category) => $category->value, self::cases());
    }
}
