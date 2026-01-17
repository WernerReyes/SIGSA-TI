<?php
namespace App\DTOs\Asset;
class AssetHistoryFiltersDto
{
    private function __construct(
        public ?array $actions = null,
        public ?string $start_date = null,
        public ?string $end_date = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            actions: $data['actions'] ?? null,
            start_date: $data['start_date'] ?? null,
            end_date: $data['end_date'] ?? null,
        );
    }
}