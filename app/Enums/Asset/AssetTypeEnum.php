<?php
namespace App\Enums\Asset;

class AssetTypeEnum
{
    const LAPTOP = 1;
    const PC = 2;
    const CELL_PHONE = 3;
    const MINI_PC = 4;
    const MONITOR = 5;
    const TABLET = 6;

    // Accesorios
    const MOUSE = 7;
    const KEYBOARD = 8;
    const HEADPHONES = 9;
    const PATCH_CABLE = 10;
    const LAPTOP_CHARGER = 11;
    const CELL_PHONE_CHARGER = 12;
    const OTHER = 13;


    public static function RRHHTypes()
    {
        return [
            self::CELL_PHONE,
            self::CELL_PHONE_CHARGER,
        ];
    }


}
