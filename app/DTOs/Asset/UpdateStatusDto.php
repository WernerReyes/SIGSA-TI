<?php
namespace App\DTOs\Asset;
class UpdateStatusDto
{
    private function __construct(
        public readonly string $status,
        public readonly ?string $description = null,
        public readonly ?string $date = null,
        public readonly ?array $images = null,
    ) { }

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            description: $data['description'] ?? null,
            date: $data['date'] ?? null,
            images: $data['images'] ?? null,
        );
    }


}