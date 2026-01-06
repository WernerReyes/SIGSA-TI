<?php
namespace App\Enums\DeliveryRecord;

enum DeliveryRecordType: string
{
    case ASSIGNMENT = 'ASSIGNMENT';
    case DEVOLUTION = 'DEVOLUTION';
    public static function values(): array
    {
        return array_map(fn(self $type) => $type->value, self::cases());
    }
}
