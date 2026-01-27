<?php
namespace App\Enums\DevelopmentRequest;

enum DevelopmentApprovalStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';

    public static function values(): array
    {
        return array_map(fn(self $status) => $status->value, self::cases());
    }

}