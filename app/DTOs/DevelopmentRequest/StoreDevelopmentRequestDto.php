<?php
namespace App\DTOs\DevelopmentRequest;

use Auth;

class StoreDevelopmentRequestDto
{
    private function __construct(
        public readonly string $title,
        public readonly string $priority,
        public readonly string $description,
        public readonly ?string $impact,
        public readonly ?int $estimated_hours,
        public readonly ?string $estimated_end_date,
        public readonly int $area_id,
        public readonly int $requested_by_id,
        public readonly ?string $requirement_file = null,
    ) {


    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            priority: $data['priority'],
            description: $data['description'],
            impact: $data['impact'] ?? null,
            estimated_hours: $data['estimated_hours'] ?? null,
            estimated_end_date: $data['estimated_end_date'] ?? null,
            area_id: $data['area_id'],
            requested_by_id: Auth::id(),
            requirement_file: $data['requirement_file'] ?? null,
        );
    }
}