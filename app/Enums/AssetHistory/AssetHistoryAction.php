<?php
namespace App\Enums\AssetHistory;

enum AssetHistoryAction: string
{
    case CREATED = 'CREATED';
    case UPDATED = 'UPDATED';

    case STATUS_CHANGED = 'STATUS_CHANGED';
    case ASSIGNED = 'ASSIGNED';
    case RETURNED = 'RETURNED';

    case DELIVERY_RECORD_UPLOADED = 'DELIVERY_RECORD_UPLOADED';

    case INVOICE_UPLOADED = 'INVOICE_UPLOADED';

    public static function values(): array
    {
        return array_map(fn(self $action) => $action->value, self::cases());
    }
}