<?php
namespace App\DTOs\Asset;
class AssignAssetDto
{
    private function __construct(
        public int $asset_id,
        public ?int $ticket_id,
        public int $assigned_to_id,
        public string $assign_date,
        public ?string $return_date,
        public ?string $comment,
        public ?array $accessories = null,
    ) {
    }


    public static function fromArray(array $data): self
    {
        return new self(
            asset_id: $data['asset_id'],
            ticket_id: $data['ticket_id'] ?? null,
            assigned_to_id: $data['assigned_to_id'],
            assign_date: $data['assign_date'],
            return_date: $data['return_date'] ?? null,
            comment: $data['comment'] ?? null,
            accessories: $data['accessories'] ?? null
        );
    }
}