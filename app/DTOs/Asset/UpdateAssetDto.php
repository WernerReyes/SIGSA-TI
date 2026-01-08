<?php
namespace App\DTOs\Asset;


class UpdateAssetDto
{
    private function __construct(
        // public int $id,
        public ?string $name,
        public ?int $type_id,
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
            // id: $data['id'],
            name: $data['name'] ?? null,
            type_id: $data['type_id'] ?? null,
            color: $data['color'] ?? null,
            brand: $data['brand'] ?? null,
            model: $data['model'] ?? null,
            serial_number: $data['serial_number'] ?? null,
            processor: $data['processor'] ?? null,
            ram: $data['ram'] ?? null,
            storage: $data['storage'] ?? null,
            purchase_date: $data['purchase_date'] ?? null,
            warranty_expiration: $data['warranty_expiration'] ?? null,
            phone: $data['phone'] ?? null,
            imei: $data['imei'] ?? null,
            is_new: $data['is_new'] ?? null,
           
        );
    }
}