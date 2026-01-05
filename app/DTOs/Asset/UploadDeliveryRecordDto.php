<?php
namespace App\DTOs\Asset;

use App\Models\AssetAssignment;
class UploadDeliveryRecordDto
{
    private function __construct(
        public readonly string $file,
        public readonly bool $is_assignment,
        public AssetAssignment $assignment,

    ) {
    }

    public static function fromArray(array $data, AssetAssignment $assignment): self
    {
        return new self(
            file: $data['file'],
            is_assignment: $data['is_assignment'],
            assignment: $assignment,
        );
    }
}