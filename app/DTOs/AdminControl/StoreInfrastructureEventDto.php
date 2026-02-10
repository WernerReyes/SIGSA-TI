<?php
namespace App\DTOs\AdminControl;

class StoreInfrastructureEventDto
{
    private function __construct(
        public string $title,
        public string $description,
        public string $type,
        public string $date,
    ) {
    }
    public static function fromArray($data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? '',
            type: $data['type'],
            date: $data['date'],
        );
    }
}