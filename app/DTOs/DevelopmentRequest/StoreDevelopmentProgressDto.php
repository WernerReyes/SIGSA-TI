<?php
namespace App\DTOs\DevelopmentRequest;


class StoreDevelopmentProgressDto
{
    private function __construct(
        public readonly int $percentage,
        public readonly ?string $notes,
        public readonly int $performedBy,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            percentage: $data['percentage'],
            notes: $data['notes'] ?? null,
            performedBy: auth()->user()->staff_id,
        );
    }
}