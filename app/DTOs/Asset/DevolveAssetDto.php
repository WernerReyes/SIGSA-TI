<?php
namespace App\DTOs\Asset;

use App\Models\AssetAssignment;
class DevolveAssetDto
{
    private function __construct(
        public readonly ?string $return_comment,
        public readonly int $responsible_id,
        public readonly string $return_date,
        public readonly string $return_reason,
        public AssetAssignment $assignment,
    ) {
    }

    public static function fromArray(array $data, AssetAssignment $assignment): self
    {
        return new self(
            return_comment: $data['return_comment'] ?? null,
            responsible_id: $data['responsible_id'],
            return_date: $data['return_date'],
            assignment: $assignment,
            return_reason: $data['return_reason'],
        );
    }
}