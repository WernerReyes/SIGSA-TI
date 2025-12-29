<?php
namespace App\DTOs\Asset;

use App\Enums\Asset\AssetStatus;
class UpdateAssetDto
{
    private function __construct(
        public int $id,
        public ?string $name,
        public ?int $type_id,
        public ?string $status,
        public ?string $brand,
        public ?string $model,
        public ?string $serial_number,
        public ?string $purchase_date,
        public ?string $warranty_expiration,
        public ?int $assigned_to = null,
    ) {
    }


    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'] ?? null,
            type_id: $data['type_id'] ?? null,
            status: $data['status'] ?? null,
            brand: $data['brand'] ?? null,
            model: $data['model'] ?? null,
            serial_number: $data['serial_number'] ?? null,
            purchase_date: $data['purchase_date'] ?? null,
            warranty_expiration: $data['warranty_expiration'] ?? null,
            assigned_to: $data['status'] === AssetStatus::ASSIGNED->value ? ($data['assigned_to'] ?? null) : null,
        );
    }
}