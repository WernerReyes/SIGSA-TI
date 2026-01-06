<?php
namespace App\DTOs\Asset;
class AssetFiltersDto
{
    private function __construct(
        public ?string $search = null,
        public ?array $status = null,
        public ?array $types = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            types: $data['types'] ?? null,
        );
    }
}