<?php
namespace App\DTOs\Asset;


class StoreAssetDto
{
    private function __construct(
        public string $name,
        public int $type_id,
        // public string $status,
        public ?string $color,
        public ?string $brand,
        public ?string $model,
        public ?string $serial_number,
        public ?string $processor,
        public ?string $ram,
        public ?string $storage,
        public ?string $phone,
        public ?string $imei,
        public ?string $purchase_date,
        public ?string $warranty_expiration,
        public ?bool $is_new,
    ) {
    }


    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            type_id: $data['type_id'],
            // status: $data['status'],
            color: $data['color'] ?? null,
            brand: $data['brand'] ?? null,
            model: $data['model'] ?? null,
            serial_number: $data['serial_number'] ?? null,
            processor: $data['processor'] ?? null,
            ram: $data['ram'] ?? null,
            storage: $data['storage'] ?? null,
            phone: $data['phone'] ?? null,
            imei: $data['imei'] ?? null,
            purchase_date: $data['purchase_date'] ?? null,
            warranty_expiration: $data['warranty_expiration'] ?? null,
            is_new: $data['is_new'] ?? null,
        );
    }
}