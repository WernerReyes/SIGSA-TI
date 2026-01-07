<?php
namespace App\DTOs\Asset;
class AssetFiltersDto
{
    private function __construct(
        public ?string $search = null,
        public ?array $status = null,
        public ?array $types = null,
        public ?array $assigned_to = null,
        public ?array $department_id = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            status: $data['status'] ?? null,
            types: $data['types'] ?? null,
            assigned_to: $data['assigned_to'] ?? null,
            department_id: $data['department_id'] ?? null,
        );
    }
}