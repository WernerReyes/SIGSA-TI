<?php
namespace App\DTOs\Asset;
class StoreAssetDto
{
    private function __construct(
        public string $name,
        public string $type,
        public string $status,
        public ?string $brand,
        public ?string $model,
        public ?string $serial_number,
        public ?string $purchase_date,
        public ?string $warranty_expiration,
    ) {
    }


    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            status: $data['status'],
            brand: $data['brand'] ?? null,
            model: $data['model'] ?? null,
            serial_number: $data['serial_number'] ?? null,
            purchase_date: $data['purchase_date'] ?? null,
            warranty_expiration: $data['warranty_expiration'] ?? null,
        );
    }
}