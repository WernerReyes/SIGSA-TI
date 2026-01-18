<?php
namespace App\DTOs\Asset;

class DevolveAssetDto
{
    private function __construct(
        public readonly ?string $return_comment,
        public readonly int $responsible_id,
        public readonly string $return_date,
        public readonly string $return_reason,
        public readonly ?array $accessories = null,
        
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            return_comment: $data['return_comment'] ?? null,
            responsible_id: $data['responsible_id'],
            return_date: $data['return_date'],
            accessories: $data['accessories'] ?? null,
            return_reason: $data['return_reason'],
        );
    }
}