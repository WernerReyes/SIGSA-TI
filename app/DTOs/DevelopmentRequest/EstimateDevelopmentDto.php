<?php
namespace App\DTOs\DevelopmentRequest;

class EstimateDevelopmentDto
{
    private function __construct(
        public readonly int $estimated_hours,
        public readonly string $estimated_end_date,
        // public readonly int $people_amount,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            estimated_hours: $data['estimated_hours'],
            estimated_end_date: $data['estimated_end_date'],
            // people_amount: $data['people_amount'],
        );
    }
}