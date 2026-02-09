<?php

namespace App\Enums\Contract;

enum ContractType: string
{
    case LICENSE = 'LICENSE';
    case SERVICE = 'SERVICE';
    case SUPPORT = 'SUPPORT';
    case HARDWARE = 'HARDWARE';
    case OTHER = 'OTHER';

    public static function values(): array
    {
        return array_map(fn (ContractType $type) => $type->value, self::cases());
    }

    public static function implodeValues(string $delimiter = ','): string
    {
        return implode($delimiter, self::values());
    }

    public static function labels(): array
    {
        return [
            self::LICENSE->value => 'Licencia',
            self::SERVICE->value => 'Servicio',
            self::SUPPORT->value => 'Soporte',
            self::HARDWARE->value => 'Hardware',
            self::OTHER->value => 'Otro',
        ];
    }

    public static function label($type): ?string
    {
        $labels = self::labels();
        return $labels[$type] ?? null;
    }
}



